<?php

namespace backend\controllers;

use common\components\GoogleAuthenticator;
use common\models\AdminUsers;
use Yii;
use yii\base\Exception;
use yii\base\UserException;
use \yii\web\HttpException;

/**
 * Site controller
 */
class SiteController extends \yii\web\Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/site/login');
        }
        $user = Yii::$app->user->identity;
        if($user->ga_secret == ''){
            return $this->redirect('/site/bind');
        }
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'blank';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        if(Yii::$app->request->isPost){
            $mobile = Yii::$app->request->post('mobile');
            $password = Yii::$app->request->post('password');
            $ga_number = Yii::$app->request->post('ga_number');
            /**
             * @var $user AdminUsers
             */
            do{
                $user = AdminUsers::find()->where(['mobile' => $mobile , 'status' => 1])->limit(1)->one();
                if(!$user){
                    Yii::$app->session->setFlash('err' , '用户不存在');
                    break;
                }
                if(!$user->validatePassword($password)){
                    Yii::$app->session->setFlash('err' , '密码错误');
                    break;
                }
                if($user->ga_secret){
                    $auth = new GoogleAuthenticator;
                    if(YII_ENV_PROD && !$auth->verifyCode($user->ga_secret ,$ga_number )){
                        Yii::$app->session->setFlash('err' , '验证器动态码错误');
                        break;
                    }
                }
                Yii::$app->user->login($user,86400);
                return $this->goHome();
            }while(false);
        }

        return $this->render('login',[
            'err' => Yii::$app->session->getFlash('err')
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);;
    }


    public function actionBind(){
        $user = AdminUsers::findOne(Yii::$app->user->id);
        if($user->ga_secret != ''){
            return $this->redirect('/');
        }
        $auth = new GoogleAuthenticator;
        $secret = Yii::$app->session->get('my_secret');
        if(Yii::$app->request->isPost){
            $number = Yii::$app->request->post('number');
            if($auth->verifyCode($secret, $number, 1)){
                $user->ga_secret = $secret;
                $user->save();
                return $this->redirect('/');
            }
        }

        if(!$secret){
            $secret = $auth->createSecret();
            Yii::$app->session->set('my_secret' , $secret);
        }
        $qrCodeContent = $auth->getQRCodeGoogleUrl(Yii::$app->user->id , $secret ,'WeiQi');

        return $this->render('bind',[
            'qrcode' => $qrCodeContent,
            'secret' => $secret
        ]);
    }

    public function actionError()
    {
        $this->layout = 'blank';
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new HttpException(404, '找不到当前页面');
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof Exception) {
            $name = $exception->getName();
        }
        if ($code) {
            $name .= $code;
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = '内部错误';
        }

        if (Yii::$app->getRequest()->getIsAjax()) {
            return "$name: $message";
        } else {
            return $this->render('error', [
                'name' => $name,
                'code' => $code,
                'message' => $message,
                'exception' => $exception,
            ]);
        }
    }
}