<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_details".
 *
 * @property int $id
 * @property int $uid
 * @property string $realname
 * @property string $contact 联系方式
 * @property string $contact_backup 备用联系方式
 * @property string $comment 备注，包括学棋经历、水平和缺点，付费意愿等，这个内容不对用户本人展示
 * @property int $client_level 客户评级， 数字越大越重要
 * @property string $updated_at
 * @property int $in_charge 负责人id，关联admin_users
 * @property string $created_at
 */
class UserDetails extends \yii\db\ActiveRecord
{
    const USER_LEVELS = [
        0 => '注册用户',
        1 => '一般付费用户',
        2 => '重要客户',
        3 => '重要客户*',
        4 => '重要客户**',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_details';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'contact', 'contact_backup'], 'required'],
            [['uid', 'client_level', 'in_charge'], 'integer'],
            [['comment'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['realname'], 'string', 'max' => 8],
            [['contact', 'contact_backup'], 'string', 'max' => 32],
            [['uid'], 'unique'],
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
            'realname' => 'Realname',
            'contact' => 'Contact',
            'contact_backup' => 'Contact Backup',
            'comment' => 'Comment',
            'client_level' => 'Client Level',
            'updated_at' => 'Updated At',
            'in_charge' => 'In Charge',
            'created_at' => 'Created At',
        ];
    }
}
