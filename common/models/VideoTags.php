<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "video_tags".
 *
 * @property int $id
 * @property int $video_id
 * @property int $tag_id
 * @property string $created_at
 */
class VideoTags extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'video_tags';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['video_id', 'tag_id'], 'required'],
            [['video_id', 'tag_id'], 'integer'],
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
            'video_id' => 'Video ID',
            'tag_id' => 'Tag ID',
            'created_at' => 'Created At',
        ];
    }
}
