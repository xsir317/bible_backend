<?php
/**
 * Created by PhpStorm.
 * User: HuJie
 * Date: 2021/9/25
 * Time: 19:47
 */

namespace api\modules\user\controllers;


use common\components\ResponseCode;
use common\models\UserAuth;
use common\repository\EmailRepo;
use common\repository\SmsRepo;
use common\repository\UserRepo;
use api\components\ClientAuthHelper;
use api\components\ClientController;

class IdentityController extends ClientController
{
    public function actionReg()
    {
        if(!\Yii::$app->request->isPost){
            return $this->renderJSON([],'请求方式错误',ResponseCode::REQUEST_ERROR);
        }

        $phone = $this->getDecryptedParam('phone');
        $nickname = $this->getDecryptedParam('nickname');
        $from = trim($this->getDecryptedParam('from' ,'client'));
        $inviter = trim($this->getDecryptedParam('inviter'));
        $inviter_uid = $inviter ? UserRepo::code2uid($inviter) : 0;
        $ad_channel = trim($this->getDecryptedParam('ad_channel'));
        $password = $this->getDecryptedParam('password');
        if(!$phone || !$nickname || !$password){
            return $this->renderJSON([],'缺少参数',ResponseCode::NEED_INIT);
        }
        $user_exist = UserRepo::getUidByAuth(UserAuth::LOGIN_TYPE_PHONE , $phone);
        if($user_exist)
        {
            return $this->renderJSON([],'此手机号已经被占用',ResponseCode::DATA_DUPLICATE);
        }
        //phone code
        $verify_code = trim($this->getDecryptedParam('verify_code'));
        if(!SmsRepo::checkVerifyCode($phone,$verify_code,'reg'))
        {
            return $this->renderJSON([],'短信校验码错误，请重试',ResponseCode::INPUT_ERROR);
        }
        $user = UserRepo::createInstance(
            $nickname,
            UserAuth::LOGIN_TYPE_PHONE,
            $phone,
            $password,
            [ 'from' => $from , 'inviter' => $inviter_uid , 'ad_channel' => $ad_channel , 'ip' => \Yii::$app->request->getRemoteIP()],
        );
        if(!$user)
        {
            return $this->renderJSON([],UserRepo::getLastErrorMsg(),ResponseCode::UNKNOWN_ERROR);
        }

        ClientAuthHelper::setCurrUser($user->id);
        return $this->renderJSON([
            'user' => UserRepo::single_user_query($this->_user()->id)
        ]);
    }

    public function actionLogin()
    {
        $phone = $this->getDecryptedParam('phone');
        $password = $this->getDecryptedParam('password');
        $user = UserRepo::pwd_login($phone,$password,\Yii::$app->request->getRemoteIP());

        if(!$user)
        {
            return $this->renderJSON([],UserRepo::getLastErrorMsg(),ResponseCode::UNKNOWN_ERROR);
        }

        ClientAuthHelper::setCurrUser($user->id);
        return $this->renderJSON([
            'user' => UserRepo::single_user_query($this->_user()->id)
        ]);
    }

    public function actionSmsLogin()
    {
        $phone = $this->getDecryptedParam('phone');
        $sms_code = trim($this->getDecryptedParam('sms_code'));
        $from = trim($this->getDecryptedParam('from'));
        $from = $from ? : 'client';
        $ad_channel = trim($this->getDecryptedParam('ad_channel'));
        $inviter = trim($this->getDecryptedParam('inviter'));
        $inviter_uid = $inviter ? UserRepo::code2uid($inviter) : 0;
        $user = UserRepo::sms_login($phone,$sms_code,$inviter_uid,$from,$ad_channel,\Yii::$app->request->getRemoteIP());

        if(!$user)
        {
            return $this->renderJSON([],UserRepo::getLastErrorMsg(),ResponseCode::UNKNOWN_ERROR);
        }

        ClientAuthHelper::setCurrUser($user->id);
        return $this->renderJSON([
            'user' => UserRepo::single_user_query($this->_user()->id)
        ]);
    }

    public function actionEmailLogin()
    {
        $email = $this->getDecryptedParam('email');
        $_code = trim($this->getDecryptedParam('code'));
        $from = trim($this->getDecryptedParam('from'));
        $from = $from ? : 'client';
        $ad_channel = trim($this->getDecryptedParam('ad_channel'));
        $inviter = trim($this->getDecryptedParam('inviter'));
        $inviter_uid = $inviter ? UserRepo::code2uid($inviter) : 0;
        $user = UserRepo::email_login($email,$_code,$inviter_uid,$from,$ad_channel,\Yii::$app->request->getRemoteIP());

        if(!$user)
        {
            return $this->renderJSON([],UserRepo::getLastErrorMsg(),ResponseCode::UNKNOWN_ERROR);
        }

        ClientAuthHelper::setCurrUser($user->id);
        return $this->renderJSON([
            'user' => UserRepo::single_user_query($this->_user()->id)
        ]);
    }

    /**
     * 微信登录， 暂时搁置
     */
    public function actionWxLogin()
    {

    }

    public function actionDetail()
    {
        $id = intval($this->get('id'));
        if(!$id)
        {
            return $this->renderJSON([],"没有id",ResponseCode::INPUT_ERROR);
        }

        $user = UserRepo::single_user_query($id);
        $detail = [];
        if($user){
            $detail = [
                'id' => $user->id,
                'nickname' => $user->nickname,
                'invite_code' => $user->getInviteCode(),
            ];
        }
        return $this->renderJSON([
            'user' => $detail,
        ]);
    }

    public function actionInfo()
    {
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        return $this->renderJSON([
            'user' => UserRepo::user_basic($this->_user()->id),
        ]);
    }

    public function actionSms()
    {
        $phone_number = trim($this->getDecryptedParam('mobile'));
        $type = trim($this->getDecryptedParam('type'));
        if($type == 'verify')
        {
            if(!$this->_user())
            {
                return $this->renderJSON([],'请先登录',ResponseCode::NOT_LOGIN);
            }
            $phone_number = $this->_user()->mobile;
        }
        if(!$phone_number)
        {
            return $this->renderJSON([],'没有电话号码',ResponseCode::INPUT_ERROR);
        }
        $code = SmsRepo::sendVerifyCode($phone_number,$type,\Yii::$app->request->getRemoteIP());
        $response = "发送成功";
        if(YII_DEBUG){
            $response .= '调试-'.$code;
        }
        return $this->renderJSON([],$response);
    }

    public function actionSendEmail()
    {
        $email = trim($this->getDecryptedParam('email'));
        $type = trim($this->getDecryptedParam('type'));
        if($type == 'verify')
        {
            if(!$this->_user())
            {
                return $this->renderJSON([],'请先登录',ResponseCode::NOT_LOGIN);
            }
            $email = $this->_user()->email;
        }
        if(!$email)
        {
            return $this->renderJSON([],'没有Email',ResponseCode::INPUT_ERROR);
        }
        $code = EmailRepo::sendVerifyCode($email,$type,\Yii::$app->request->getRemoteIP());
        $response = "发送成功";
        if(1){
            $response .= '调试-'.$code;
        }
        return $this->renderJSON(['msg' => $response],$response);
    }

    public function actionLogout()
    {
        ClientAuthHelper::setCurrUser(0);
        return $this->renderJSON();
    }
}