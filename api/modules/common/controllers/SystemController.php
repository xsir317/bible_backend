<?php

namespace api\modules\common\controllers;

use api\components\ClientAuthHelper;
use api\components\ClientController;
use common\components\ResponseCode;

class SystemController extends ClientController
{
    public function actionInit(){
        $init_token = ClientAuthHelper::generateToken();
        return  $this->renderJSON($init_token);
    }

    /**
     * @return void|\yii\console\Response|\yii\web\Response
     */
    public function actionSetKey(){
        $session_id = $this->get('session_id');
        $encrypted = $this->get('encrypted');
        if(ClientAuthHelper::setAESKey($session_id , $encrypted)){
            return  $this->renderJSON();
        }else{
            return  $this->renderJSON([],'init again' , ResponseCode::NEED_INIT);
        }
    }
}