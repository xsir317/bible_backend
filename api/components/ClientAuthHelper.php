<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/11/26
 * Time: 13:55
 */

namespace api\components;



use common\repository\LogRepo;
use yii\web\Request;

/**
 * Class ClientAuthHelper
 * @package frontend\components
 * 处理header 相关的身份校验和redis里的数据读写
 */
class ClientAuthHelper
{
    private static $session_id = '';
    private static $aes_key = '';
    const AES_KEY_LEN = 32;
    const SESSION_LIFE_TIME = 86400 * 30;

    public static function generateToken()
    {
        $session_id =   md5(microtime().rand(100000,999999).'token');

        $config = [
            "digest_alg" => "sha256",
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ];
        $res = openssl_pkey_new($config);
        openssl_pkey_export($res, $privateKey);
        $publicKey = openssl_pkey_get_details($res)['key'];

        \Yii::$app->redis->hSet("sess:{$session_id}", 'private' , $privateKey);
        \Yii::$app->redis->expire("sess:{$session_id}" , (YII_ENV_PROD ? 60 : 86400 * 30));
        return [
            'session_id' => $session_id,
            'public' => $publicKey,
        ];
    }

    public static function setAESKey($session_id , $encrypted_key){
        //初始化步骤：set 对称密钥
        $privateKey = \Yii::$app->redis->hGet("sess:{$session_id}", 'private');
        if(!$privateKey){
            return false;
        }
        \Yii::$app->redis->hDel("sess:{$session_id}", 'private');
        openssl_private_decrypt(base64_decode($encrypted_key), $decryptedKey, $privateKey,OPENSSL_PKCS1_OAEP_PADDING);

        if(strlen($decryptedKey) != static::AES_KEY_LEN){
            return false;
        }
        \Yii::$app->redis->hSet("sess:{$session_id}" , 'aes_key' , $decryptedKey);
        \Yii::$app->redis->expire("sess:{$session_id}" , static::SESSION_LIFE_TIME);
        return true;
    }

    public static function renewKey($session_id , $new_key){
        \Yii::$app->redis->hSet("sess:{$session_id}" , 'aes_key' , $new_key);
        \Yii::$app->redis->expire("sess:{$session_id}" , static::SESSION_LIFE_TIME);
        return true;
    }

    public static function setCurrUser($uid){
        $session_id = self::$session_id;
        if($session_id){
            \Yii::$app->redis->hSet("sess:{$session_id}" , 'uid' , $uid);
            return true;
        }
        return false;
    }

    public static function getCurrUser(){
        $session_id = self::$session_id;
        if($session_id){
            return intval(\Yii::$app->redis->hGet("sess:{$session_id}" , 'uid' ));
        }
        return 0;
    }

    /**
     * 检查是否允许访问
     * @param $request Request
     * @return bool|array
     */
    public static function checkAccessToken($request)
    {
        $headers = $request->getHeaders();
        $session_id = $headers['x-session-id'] ?? null;
        if(!$session_id){
            return false;
        }
        self::$session_id = $session_id;

        $uri = $request->getUrl();
        $post_data = $request->getRawBody();
        //decode
        if(YII_ENV_PROD){
            $aes_key = \Yii::$app->redis->hGet("sess:{$session_id}" , 'aes_key');
            if(!$aes_key){
                return false;
            }
            self::$aes_key = $aes_key;
            if(!self::checkTimestamp($headers['timestamp'] ?? 0)){
                return false;
            }

            if(!self::checkSignature($headers['timestamp'] , $headers['checksum'], $aes_key, $uri , $post_data))
            {
                return false;
            }
            //TODO 可以不要每次都操作
            \Yii::$app->redis->expire("sess:{$session_id}",static::SESSION_LIFE_TIME);
            $tmp_split = explode(':' , $post_data);

            $iv = base64_decode($tmp_split[0]);
            $cipherText = base64_decode($tmp_split[1]);
            $decryptedData = openssl_decrypt($cipherText, 'AES-256-CBC', $aes_key, OPENSSL_RAW_DATA, $iv);
        }else{
            $decryptedData = $post_data;
        }
        return @json_decode($decryptedData, true);
    }

    public static function encrypt($string){
        if(empty(self::$aes_key)){
            return false;
        }
        $iv = openssl_random_pseudo_bytes(16); // 生成 16 字节 IV

        $encryptedData = openssl_encrypt($string, 'AES-256-CBC', self::$aes_key, OPENSSL_RAW_DATA, $iv);

        // Base64 编码 IV 和 加密数据，格式 "IV:密文"
        return base64_encode($iv) . ":" . base64_encode($encryptedData);
    }

    private static function checkSignature($timestamp, $signature, $secret ,$uri, $post_data)
    {
        if(!YII_ENV_PROD){
            return true;
        }
        $sig_str = "REQUEST_URI={$uri}&content={$post_data}&timestamp={$timestamp}&secret_key={$secret}";

        $calc_sig = md5($sig_str) ;

        if(strtoupper($signature) !== strtoupper($calc_sig)) {
            LogRepo::file_log(\Yii::$app->getRuntimePath() . '/logs/auth.log',[
                'md5_origin' => $sig_str,
                'md5_calc' => $calc_sig,
                'md5_header' => $signature,
                'header_time' => date('Y-m-d H:i:s',$timestamp),
            ]);
            return false;
        }
        return true;
    }

    private static function checkTimestamp($timestamp) {
        if(!YII_ENV_PROD){
            return true;
        }
        if( abs(time() - intval($timestamp)) <= 86400 ) {
            return true;
        }
        return false;
    }

    /**
     * 通过UserAgent 检查版本。 暂时不用。
     * @return boolean
     */
    private static function checkUserAgent()
    {
        $acceptable_version = '2.3.44';
        $ua = \Yii::$app->request->getUserAgent();
        $exploded = explode(';',$ua);
        $curr_version = '0.0';
        foreach ($exploded as $val)
        {
            $kv = explode(':',$val);
            if($kv[0] == 'ver')
            {
                $curr_version = $kv[1];
                break;
            }
        }
        $version_ok = version_compare($curr_version,$acceptable_version,'>=');
        return $version_ok;
    }
}