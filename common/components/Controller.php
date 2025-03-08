<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2017/12/4
 * Time: 14:36
 */

namespace common\components;


class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;

    protected function renderJSON($data=[], $msg ="ok", $code = 200)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        \Yii::$app->response->data = [
            "code" => $code,
            "msg"   =>  $msg,
            "data"  =>  $data,
            "req_id" =>  uniqid(),
        ];

        return \Yii::$app->response;
    }

    public function post($key, $default = "") {
        return \Yii::$app->request->post($key, $default);
    }


    public function get($key, $default = "") {
        return \Yii::$app->request->get($key, $default);
    }
}
