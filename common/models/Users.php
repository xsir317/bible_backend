<?php

namespace common\models;

use common\components\StringHelper;
use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $nickname
 * @property int $coins 用户的代币
 * @property int $grade 评级，负数表示级位正数表示段位。 0表示尚未评估
 * @property string $created_at
 */
class Users extends \yii\db\ActiveRecord
{
    const USR_INVITE_MAP = 'GDWUSKJBCXYZNEHAMP863R5Q729T4';
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coins', 'grade'], 'integer'],
            [['created_at'], 'safe'],
            [['nickname'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nickname' => 'Nickname',
            'coins' => 'Coins',
            'grade' => 'Grade',
            'created_at' => 'Created At',
        ];
    }

    public static function getUserById($uid){
        return self::findOne( intval($uid) );
    }

    public static function Code2Uid($code){
        return StringHelper::code2num($code,self::USR_INVITE_MAP);
    }

    public function getInviteCode(){
        return StringHelper::num2code($this->id,self::USR_INVITE_MAP);
    }
}
