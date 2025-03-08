<?php
/**
 * Created by PhpStorm.
 * User: HuJie
 * Date: 2021/9/25
 * Time: 19:47
 */

namespace api\modules\user\controllers;


use common\components\ResponseCode;
use common\models\Users;
use common\repository\UserRepo;
use api\components\ClientController;

class SettingsController extends ClientController
{
    public function actionEdit(){
        if(!$this->_user())
        {
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }
        $nickname = trim($this->post('nickname'));
        $gender = intval(trim($this->post('gender')));
        $birthday = trim($this->post('birthday'));

        $user = Users::findOne($this->_user()->id);
        if(!$user->is_profile_settled()){
            if($gender){
                $user->gender = ($gender == 1 ? 1:2);
            }
            if($birthday){
                $user->birthday = $birthday;
            }
        }
        if($nickname){
            $user->nickname = $nickname;
        }
        if(!$user->save()){
            return $this->renderJSON([] , $user->getFirstError() ,ResponseCode::UNKNOWN_ERROR);
        }
        Users::clearGetByIdCache($this->_user()->id);

        return $this->renderJSON([
            'user' => UserRepo::user_basic($this->_user()->id),
        ]);
    }

    public function actionAvatar(){

    }
}