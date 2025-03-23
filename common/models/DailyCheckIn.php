<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "daily_check_in".
 *
 * @property int $id
 * @property int $uid
 * @property string $start_date
 * @property string $last_check_date 最后一次签到的日期
 * @property int $days_count
 * @property string $updated_time
 * @property string $created_time
 */
class DailyCheckIn extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'daily_check_in';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['days_count'], 'default', 'value' => 0],
            [['created_time'], 'default', 'value' => '2023-01-01 08:00:00'],
            [['uid', 'start_date', 'last_check_date'], 'required'],
            [['uid', 'days_count'], 'integer'],
            [['start_date', 'last_check_date', 'updated_time', 'created_time'], 'safe'],
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
            'start_date' => 'Start Date',
            'last_check_date' => 'Last Check Date',
            'days_count' => 'Days Count',
            'updated_time' => 'Updated Time',
            'created_time' => 'Created Time',
        ];
    }

}
