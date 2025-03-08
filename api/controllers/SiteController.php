<?php

namespace api\controllers;

use yii\base\UserException;
use yii\web\Controller;
use yii\web\HttpException;
use Yii;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return 'api';
    }

    public function actionError(){
        if (($exception = Yii::$app->getErrorHandler()->exception) === null) {
            $exception = new HttpException(404);
        }

        if ($exception instanceof HttpException) {
            $code = $exception->statusCode;
        } else {
            $code = $exception->getCode();
        }
        if ($exception instanceof \Exception) {
            $name = $exception->getName();
        } else {
            $name = Yii::t('app/api', 'Error');
        }
        if ($code) {
            $name .= " (#$code)";
        }

        if ($exception instanceof UserException) {
            $message = $exception->getMessage();
        } else {
            $message = Yii::t('app/api', 'An internal server error occurred.');
        }
        \Yii::$app->response->setStatusCode($code);

        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = [
            "code" => $code,
            "msg"   =>  $name . " :" . $message,
            "data"  =>  [],
            "req_id" =>  uniqid(),
        ];

        return \Yii::$app->response;
    }
}
