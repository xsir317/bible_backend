<?php

namespace common\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "admin_users".
 *
 * @property int $id
 * @property string $name
 * @property string $mobile
 * @property string $password
 * @property string $cmt
 * @property string $ga_secret
 * @property int $status
 * @property int $created_at
 */
class AdminUsers extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'admin_users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'mobile', 'password', 'status', 'created_at'], 'required'],
            [['status', 'created_at'], 'integer'],
            [['name'], 'string', 'max' => 8],
            [['mobile'], 'string', 'max' => 20],
            [['password', 'cmt'], 'string', 'max' => 120],
            [['ga_secret'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'cmt' => 'Cmt',
            'ga_secret' => 'Ga Secret',
            'status' => 'Status',
            'created_at' => 'Created At',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return false;
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return '';
    }

    public function validateAuthKey($authKey)
    {
        return true;
    }


    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function setPassword($password)
    {
        $this->password = Yii::$app->security->generatePasswordHash($password);
    }
}
