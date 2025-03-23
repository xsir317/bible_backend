<?php

namespace api\modules\user\controllers;

use common\components\ResponseCode;
use api\components\ClientController;
use common\repository\DailySignRepo;
use common\repository\UserRepo;

class SummaryController extends ClientController
{
    public function actionInfo(){
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }
        return $this->renderJSON([
            'detail' => UserRepo::getDetail($this->_user()->id),
            'checkin_info' => DailySignRepo::status($this->_user()->id)
        ]);
    }
}