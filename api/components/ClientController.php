<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2021/09/23
 * Time: 17:20
 */

namespace api\components;


use common\components\Controller;
use common\components\ResponseCode;
use common\models\Users;

class ClientController extends Controller
{
    //TODO 搞一个像样的通信机制
    private $_user = null;
    //只有init接口不需要校验header，其他接口都要校验。
    private $ignore_check_header = [
        'common/system/init',
        'common/system/set-key',
        'common/system/time',
    ];
    private $decrypted_params = [];
    public $enableCsrfValidation = false;
    public function beforeAction($action)
    {
        //先确认通信合法：
        //对于要校验合法性的请求，校验 POST内容 和 header 是否对得上（hash）。
        //对不上的， 可能是非法请求
        //对上了， 就 decode 一下， 请求参数存到static 里。
        $route = $this->getRoute();
        if(!in_array($route,$this->ignore_check_header))
        {
            $this->decrypted_params = ClientAuthHelper::checkAccessToken(\Yii::$app->request);
            if(YII_ENV_PROD && empty($this->decrypted_params) )
            {
                return false;
            }
        }
        /* TODO 更新下活跃时间*/
        return parent::beforeAction($action);
    }

    /**
     * @return Users|null
     */
    public function _user()
    {
        if(!$this->_user)
        {
            $uid = ClientAuthHelper::getCurrUser();
            if($uid)
            {
                $this->_user = Users::getUserById($uid);
            }
        }
        return $this->_user;
    }

    public function get($key, $default = "") {
        return $this->getDecryptedParam($key) ?: parent::get($key, $default);
    }

    public function getDecryptedParam($name,$default = null){
        return $this->decrypted_params[$name] ?? $default;
    }
}
