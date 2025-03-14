<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_auth".
 *
 * @property int $id
 * @property int $uid
 * @property string $type 登录方式类型
 * @property string $unionid
 * @property string $openid
 * @property string $passwd 密码
 * @property string $info 其它信息
 * @property int $created_at
 * @property string $deleted_at
 */
class UserAuth extends \yii\db\ActiveRecord
{
    const LOGIN_TYPE_PHONE = 'phone';
    const LOGIN_TYPE_EMAIL = 'email';
    const LOGIN_TYPE_WX = 'weixin';
    const LOGIN_TYPE_WEIBO = 'weibo';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid',  'deleted_at'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['unionid', 'openid', 'passwd'], 'string', 'max' => 120],
            [['info'], 'string', 'max' => 10000],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uid' => 'Uid',
            'type' => 'Type',
            'unionid' => 'Unionid',
            'openid' => 'Openid',
            'passwd' => 'Passwd',
            'info' => 'Info',
            'created_at' => 'Created At',
            'deleted_at' => 'Deleted At',
        ];
    }
}
