<?php

namespace api\modules\user\controllers;

use api\components\ClientController;
use common\components\ResponseCode;
use common\repository\DailySignRepo;

class CheckInController extends ClientController
{
    public function actionStatus(){
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        return $this->renderJSON([
            'checkin_info' => DailySignRepo::status($this->_user()->id)
        ]);
    }

    public function actionToday(){
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }
        //TODO 奖励，暂时没有， 就签到一下
        $rewards = DailySignRepo::doCheckIn($this->_user()->id);
        $error = DailySignRepo::getLastErrorMsg();
        if($error){
            return $this->renderJSON([] , $error , ResponseCode::UNKNOWN_ERROR);
        }else{
            return $this->renderJSON([
                'checkin_info' => DailySignRepo::status($this->_user()->id)
            ]);
        }
    }

    public function actionFillUp(){
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }
        //TODO 奖励，暂时没有， 就签到一下
        $rewards = DailySignRepo::doFillUpCheckIn($this->_user()->id);
        $error = DailySignRepo::getLastErrorMsg();
        if($error){
            return $this->renderJSON([] , $error , ResponseCode::UNKNOWN_ERROR);
        }else{
            return $this->renderJSON([
                'checkin_info' => DailySignRepo::status($this->_user()->id)
            ]);
        }
    }

}