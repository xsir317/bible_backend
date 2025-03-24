<?php
/**
 * Created by PhpStorm.
 * 先做最简单的，后续可以参考塔罗那个项目把更好用的功能加进来
 */

namespace common\repository;


use common\components\StringHelper;
use common\models\UserAttributes;
use common\models\UserAuth;
use common\models\UserDetails;
use common\models\Users;
use yii\web\User;

class UserRepo extends BaseRepo
{
    private static $invite_code_dict = 'abcdefghjkmnpqrstuvwxy3456789';

    /**
     * @param $phone
     * @param $password
     * @param $ip
     * @return bool|\common\models\Users
     */
    public static function pwd_login($phone, $password, $ip='')
    {
        if(!$phone)
        {
            return self::_err('请输入手机号');
        }
        if($ip && !self::recent_history_check($ip))
        {
            return self::_err('登录过于频繁');
        }
        $userAuth = UserAuth::find()
            ->where(['unionid' => $phone ,'type' => UserAuth::LOGIN_TYPE_PHONE])
            ->limit(1)
            ->one();
        if($userAuth){
            $user = Users::findOne($userAuth->uid);
            if($user){
                if($user->status != 1)
                {
                    return self::_err('用户已被封禁');
                }
                if(!self::verify_pwd($password,$userAuth->passwd))
                {
                    return self::_err('密码错误');
                }
                return $user;
            }
        }
        return self::_err('无此用户');
    }

    public static function sms_login($phone, $sms_code, $inviter_uid, $reg_from, $reg_ad_channel, $ip='')
    {
        if(!$phone)
        {
            return self::_err('请输入手机号');
        }
        if(!$sms_code)
        {
            return self::_err('请输入校验码');
        }
        if($ip && !self::recent_history_check($ip))
        {
            return self::_err('登录过于频繁');
        }
        if(!SmsRepo::checkVerifyCode($phone,$sms_code,'login'))
        {
            return self::_err('短信验证码错误');
        }
        $uid = self::getUidByAuth(UserAuth::LOGIN_TYPE_PHONE ,$phone);
        if($uid){
            $user = Users::findOne($uid);
        }else{
            $user = self::createInstance(
                '-' , //TODO 随机给个名字 TODO 可修改昵称 和 密码
                UserAuth::LOGIN_TYPE_PHONE ,
                $phone ,
                '',
                [ 'from' => $reg_from , 'inviter' => $inviter_uid , 'ad_channel' => $reg_ad_channel, 'ip' => $ip],
            );
        }
        if($user)
        {
            return $user;
        }
        return self::_err('无此用户');
    }

    public static function email_login($email, $code, $inviter_uid, $reg_from, $reg_ad_channel, $ip='')
    {
        if(!$email)
        {
            return self::_err('请输入Email');
        }
        if(!$code)
        {
            return self::_err('请输入校验码');
        }
        if($ip && !self::recent_history_check($ip))
        {
            return self::_err('登录过于频繁');
        }
        if(!SmsRepo::checkVerifyCode($email,$code,'login'))
        {
            return self::_err('验证码错误');
        }
        $uid = self::getUidByAuth(UserAuth::LOGIN_TYPE_EMAIL ,$email);
        if($uid){
            $user = Users::findOne($uid);
        }else{
            $user = self::createInstance(
                '-' , //TODO 随机给个名字 TODO 可修改昵称 和 密码
                UserAuth::LOGIN_TYPE_EMAIL ,
                $email ,
                '',
                [ 'from' => $reg_from , 'inviter' => $inviter_uid , 'ad_channel' => $reg_ad_channel, 'ip' => $ip],
            );
        }
        if($user)
        {
            return $user;
        }
        return self::_err('无此用户');
    }

    public static function oauth_login($type , $token){

    }

    public static function bind_phone(){

    }

    public static function bind_oauth(){

    }

    public static function getUidByAuth($type , $unionid){
        $auth = UserAuth::find()
            ->where(['type' => $type , 'unionid' => $unionid])
            ->limit(1)
            ->one();
        return $auth->uid ?? 0;
    }

    /**
     * @param $nickname
     * @param $auth_type
     * @param $unionid
     * @param $pwd
     * @param $detail
     * @return bool|Users
     * @throws \yii\db\Exception
     */
    public static function createInstance( $nickname, $auth_type , $unionid, $pwd , $detail)
    {
        $exist = static::getUidByAuth($auth_type , $unionid);
        if($exist)
        {
            return self::_err('注册失败，此用户已经存在');
        }
        $user = new Users();
        $user->nickname = $nickname;
        $user->coins = 0;
        $user->grade = 0;
        $user->created_at = date('Y-m-d H:i:s');
        if(!$user->save(0) || !$user->id){
            return self::_err('注册失败，请重试');
        }

        $auth = new UserAuth();
        $auth->uid = $user->id;
        $auth->type = $auth_type;
        $auth->unionid = $unionid;
        $auth->openid = '';
        $auth->passwd = $pwd ? self::hash_pwd($pwd) : '';
        $auth->info = empty($detail) ? '' : json_encode($detail);
        $auth->created_at = $user->created_at;
        $auth->deleted_at = 0;
        $auth->save(0);
        return $user;
    }

    public static function hash_pwd($password)
    {
        return password_hash($password , PASSWORD_DEFAULT);
    }

    public static function verify_pwd($password,$hash)
    {
        return password_verify($password , $hash);
    }

    private static function recent_history_check($ip)
    {
        $time = time();
        if($ip)
        {
            $total = 0;
            for($i=4;$i>=0;$i--)
            {
                $hash_key = sprintf("login:ip:%s:%u",date('Hi',$time-60*$i),ip2long($ip));
                $total = $total + intval(\Yii::$app->redis->get($hash_key));
            }
            if($total >= 20)
            {
                return false;
            }
            $hash_key = sprintf("login:ip:%s:%u",date('Hi',$time),ip2long($ip));
            \Yii::$app->redis->incr($hash_key);
            \Yii::$app->redis->expire($hash_key,600);
        }
        return true;
    }

    /**
     * 获取用户信息 id => detail 数组 map
     * @param $uids array
     * @return array
     */
    public static function user_batch_query($uids , $curr_uid = 0){
        $query = Users::find()
            ->where(['id' => $uids])
            ->indexBy('id')
            ->all();
        return $query;
    }

    public static function single_user_query($uid , $curr_uid = 0){
        $query = self::user_batch_query([$uid]);
        return $query[$uid] ?? [];
    }

    /**
     * 在指定array 的每个元素上附加上用户信息
     * @param $data array
     * @param $curr_uid
     * @param $uid_column
     * @param $user_column
     * @return void
     */
    public static function render_user(&$data ,$curr_uid = 0 , $uid_column = 'uid' , $user_column = 'user'){
        $uids = array_column($data , $uid_column);
        $user_data = self::user_batch_query($uids,$curr_uid);
        foreach ($data as $k=>$v){
            $data[$k][$user_column] = $user_data[$v[$uid_column]] ?? [];
        }
    }

    public static function uid2code($uid){
        return StringHelper::num2code($uid,self::$invite_code_dict);
    }

    /**
     * @param $code string
     * @return int
     */
    public static function code2uid($code){
        $code = strtolower(trim($code));
        return StringHelper::code2num($code,self::$invite_code_dict);
    }

    public static function getLevel($exp){
        $level = 0;
        foreach (self::$level_exp_table as $l => $v){
            if($exp < $v){
                return [$level , $v-$exp];
            }
            $level = $l;
        }
        return [$level , 0];
    }

    /**
     * @param $level int
     * @return string
     */
    public static function showLevelStr($level){
        if($level == 0){
            return '未知';
        }
        return abs($level) . ($level > 0 ? '段':'级');
    }

    public static function getAvailableLevels(){
        $ret = [];
        for ($i = -20 ; $i <= 7 ; $i ++){
            $ret[$i] = static::showLevelStr($i);
        }
        return $ret;
    }

    /**
     * 根据uid 获取用户详情， TODO 缓存， 性能优化
     * @param $uid
     * @return array | null
     */
    public static function getDetail($uid){
        $user = Users::findOne($uid);
        if(!$user){
            return null;
        }
        return [
            'id' => $user->id,
            'nickname' => $user->nickname,
            'coins' => $user->coins,
            'avatar' => '/avatar1.png',
        ];
    }
}