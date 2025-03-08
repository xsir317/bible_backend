<?php

namespace common\models;

use common\repository\ContentRepo;
use Yii;

/**
 * This is the model class for table "videos".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $level
 * @property int $length
 * @property string $url
 * @property string $created_at
 */
class Videos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'videos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at'], 'safe'],
            [['level','length'], 'integer'],
            [['title'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 1024],
            [['url'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => '标题',
            'description' => '简介',
            'level' => '级别',
            'url' => '播放地址',
            'length' => '时长（秒）',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return array
     */
    public function getTags(){
        $tag_ids = VideoTags::find()
            ->select('tag_id')
            ->where(['video_id' => $this->id])
            ->column();
        return ContentRepo::renderTags($tag_ids);
    }
}
