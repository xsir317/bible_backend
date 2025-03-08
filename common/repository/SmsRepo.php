<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2021/9/27
 * Time: 10:44
 */

namespace common\repository;


use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Sms\V20210111\Models\SendSmsRequest;
use TencentCloud\Sms\V20210111\SmsClient;

class SmsRepo extends BaseRepo
{
    private static $_default_sign = '塔罗屋';
    private static $templates = [
        'reg' => '656891',
        'login' => '1059598',
        'change_pwd' => '674368',
        'verify' => '674370',
        'init_pwd' => '674373',
    ];

    private static  $foreign_templates = [
        'reg' => '656891',
        'login' => '1070692',
        'change_pwd' => '1070674',
        'verify' => '1070676',
        'init_pwd' => '1070680',
    ];

    public static function dosend($mobile, $template, $params , $sign='')
    {
        if(!$mobile)
            return self::_err('手机号不能为空');
        $is_foreign = false;
        if($mobile[0] == '+' && substr($mobile,0,3) != '+86')
        {
            $is_foreign = true;
        }
        $tpl_grp = $is_foreign ? self::$foreign_templates : self::$templates;
        $tpl_id = isset($tpl_grp[$template]) ? $tpl_grp[$template] : $tpl_grp['reg'];
        if(!$sign)
        {
            $sign = self::$_default_sign;
        }

        try {
            /* 必要步骤：
             * 实例化一个认证对象，入参需要传入腾讯云账户密钥对 secretId 和 secretKey
             * CAM 密钥查询：https://console.cloud.tencent.com/cam/capi */
            $accessKeyId = "";
            $accessKeySecret = "";
            $cred = new Credential($accessKeyId, $accessKeySecret);
            //$cred = new Credential(getenv("TENCENTCLOUD_SECRET_ID"), getenv("TENCENTCLOUD_SECRET_KEY"));

            // 实例化一个 http 选项，可选，无特殊需求时可以跳过
            $httpProfile = new HttpProfile();
            $httpProfile->setReqMethod("GET");  // POST 请求（默认为 POST 请求）
            $httpProfile->setReqTimeout(30);    // 请求超时时间，单位为秒（默认60秒）
            $httpProfile->setEndpoint("sms.tencentcloudapi.com");  // 指定接入地域域名（默认就近接入）

            // 实例化一个 client 选项，可选，无特殊需求时可以跳过
            $clientProfile = new ClientProfile();
            $clientProfile->setSignMethod("TC3-HMAC-SHA256");  // 指定签名算法（默认为 HmacSHA256）
            $clientProfile->setHttpProfile($httpProfile);
            $client = new SmsClient($cred, "ap-shanghai", $clientProfile);

            // 实例化一个 sms 发送短信请求对象，每个接口都会对应一个 request 对象。
            $req = new SendSmsRequest();

            /* 填充请求参数，这里 request 对象的成员变量即对应接口的入参
               * 短信控制台：https://console.cloud.tencent.com/smsv2
               * sms helper：https://cloud.tencent.com/document/product/382/3773 */
            /* 短信应用 ID: 在 [短信控制台] 添加应用后生成的实际 SDKAppID，例如1400006666 */
            $req->SmsSdkAppid = "1400397035";
            /* 短信签名内容: 使用 UTF-8 编码，必须填写已审核通过的签名，可登录 [短信控制台] 查看签名信息 */
            $req->Sign = $sign;
            /* 短信码号扩展号: 默认未开通，如需开通请联系 [sms helper] */
            $req->ExtendCode = "0";
            /* 下发手机号码，采用 e.164 标准，+[国家或地区码][手机号]
               * 例如+8613711112222， 其中前面有一个+号 ，86为国家码，13711112222为手机号，最多不要超过200个手机号*/
            $fixed_mobile = $mobile;
            if(!$is_foreign && $mobile[0] != '+')
            {
                $fixed_mobile = '+86'.$mobile;
            }
            $req->PhoneNumberSet = [$fixed_mobile];
            /* 国际/港澳台短信 senderid: 国内短信填空，默认未开通，如需开通请联系 [sms helper] */
            $req->SenderId = $is_foreign ? "Qcloud" : "";
            /* 用户的 session 内容: 可以携带用户侧 ID 等上下文信息，server 会原样返回 */
            $req->SessionContext = "";
            /* 模板 ID: 必须填写已审核通过的模板 ID。可登录 [短信控制台] 查看模板 ID */
            $req->TemplateID = $tpl_id;
            /* 模板参数: 若无模板参数，则设置为空*/
            if(empty($params))
            {
                $req->TemplateParamSet = null;
            }
            elseif(is_array($params))
            {
                $req->TemplateParamSet = array_values($params);
            }
            elseif(is_string($params))
            {
                $req->TemplateParamSet = [
                    $params,
                ];
            }


            // 通过 client 对象调用 SendSms 方法发起请求。注意请求方法名与请求对象是对应的
            $resp = $client->SendSms($req);
            return $resp->toJsonString();
        }
        catch(\Exception $e) {
            //echo $e;
            return self::_err($e->getMessage());
        }
    }

    public static function send($mobile, $template, $params, $sign='', $ip = '')
    {
        if(YII_DEBUG)
        {
            return self::dosend($mobile,$template,$params,$sign);
        }
        else
        {
            if(self::recent_history_check($mobile,$ip))
            {
                LogRepo::file_log(\Yii::$app->getRuntimePath().'/logs/sms.log',[
                    'event' => 'send',
                    'type' => $template,
                    'mobile' => $mobile,
                    'ip' => $ip,
                ]);
            }
            else
            {
                LogRepo::file_log(\Yii::$app->getRuntimePath().'/logs/sms.log',[
                    'event' => 'abuse_blocked',
                    'type' => $template,
                    'mobile' => $mobile,
                    'ip' => $ip,
                ]);
            }
        }
        return true;
    }

    private static function recent_history_check($mobile,$ip)
    {
        $redis_conn = \Yii::$app->redis;
        $time = time();
        if($mobile)
        {
            $mobile_cache_key = sprintf("SMS:mobile:%s",$mobile);
            if($last_send = intval($redis_conn->get($mobile_cache_key)))
            {
                if($last_send > ($time-29))
                {
                    return false;
                }
            }
            $minute_total = 0;
            for ($i=5;$i>=0;$i--)
            {
                $minute_hash_key = sprintf("SMS:num:%s:%s",substr( date('Hi',$time-600*$i),0,3),$mobile);
                $minute_total  += intval($redis_conn->get($minute_hash_key));
            }
            if($minute_total >= 6)
            {
                return false;
            }
            $day_hash_key = sprintf("SMS:numd:%s:%s", date('md'),$mobile);
            if(intval($redis_conn->get($day_hash_key)) > 10)
            {
                return false;
            }
            $minute_hash_key = sprintf("SMS:num:%s:%s",substr( date('Hi',$time),0,3),$mobile);
            $redis_conn->incr($minute_hash_key);
            $redis_conn->expire($minute_hash_key,3600);
            $redis_conn->incr($day_hash_key);
            $redis_conn->expire($day_hash_key,86400);
            $redis_conn->set($mobile_cache_key,$time,60);
        }
        if($ip)
        {
            $ip_black_list = 'SMS:ip:block:'.$ip;
            if(\Yii::$app->redis->get($ip_black_list))
            {
                return false;
            }
            $total = 0;
            for($i=5;$i>=0;$i--)
            {
                $hash_key = sprintf("SMS:ip:%s:%s",substr(date('Hi',$time-60*$i),0,3),$ip);
                $total = $total + intval(\Yii::$app->redis->get($hash_key));
            }
            $hash_key = sprintf("SMS:ip:%s:%s",substr(date('Hi',$time),0,3),$ip);
            $redis_conn->incr($hash_key);
            $redis_conn->expire($hash_key,4200);
            if($total >= 20)
            {
                \Yii::$app->redis->set($ip_black_list,1,86400);
                return false;
            }
        }
        return true;
    }

    /**
     * 用于发送验证码，code可以自动生成
     * TODO 缓存要带上 type
     * @param $mobile
     * @param string $type
     * @param string $ip 发短信的ip
     * @return bool
     */
    public static function sendVerifyCode($mobile,$type='reg',$ip = '')
    {
        if(!$mobile)
        {
            return self::_err('手机号为空');
        }
        $code = sprintf("%06d",rand(100,999999));
        \Yii::$app->cache->set(self::cache_key($mobile,$type),$code,600);
        if(YII_DEBUG){
            return $code;
        }
        return self::send($mobile,$type,$code,'',$ip);
    }

    /**
     * 检查验证码
     * @param $mobile
     * @param $code
     * @return bool
     */
    public static function checkVerifyCode($mobile,$code,$type)
    {
        $origin_code = \Yii::$app->cache->get(self::cache_key($mobile,$type));
        \Yii::$app->cache->delete(self::cache_key($mobile,$type));
        LogRepo::file_log(\Yii::$app->getRuntimePath().'/sms_verify.log',[
            'action' => 'verify',
            'mobile' => $mobile,
            'input_code' => $code,
            'origin_code' => $origin_code
        ]);
        return $code && ($origin_code == $code);
    }

    private static function cache_key($mobile,$type)
    {
        return sprintf("vcode::%s:%s",$mobile,$type);
    }
}