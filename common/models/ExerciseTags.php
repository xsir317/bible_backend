<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "exercise_tags".
 *
 * @property int $id
 * @property int $exec_id
 * @property int $tag_id
 * @property string $created_at
 */
class ExerciseTags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exercise_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['exec_id', 'tag_id'], 'required'],
            [['exec_id', 'tag_id'], 'integer'],
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
            'exec_id' => 'Exec ID',
            'tag_id' => 'Tag ID',
            'created_at' => 'Created At',
        ];
    }
}
