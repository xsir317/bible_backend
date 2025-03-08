<?php

namespace common\models;

use common\repository\ContentRepo;
use Yii;

/**
 * This is the model class for table "exercises".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property int $level
 * @property string $question 题目的棋盘状态
 * @property string $answer 解答
 * @property int $try_times 尝试次数
 * @property int $cleared_times 成功次数
 * @property int $failed_times
 * @property string $created_at
 */
class Exercise extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exercises';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'description', 'answer'], 'required'],
            [['try_times', 'cleared_times', 'failed_times', 'level'], 'integer'],
            [['try_times', 'cleared_times', 'failed_times'], 'default', 'value' => 0],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['description'], 'string', 'max' => 1024],
            [['question', 'answer'], 'string', 'max' => 2048],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => '简介',
            'level' => '级别',
            'question' => '习题',
            'answer' => '解答',
            'try_times' => '总尝试次数',
            'cleared_times' => '解答成功次数',
            'failed_times' => '解答失败次数',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return array
     */
    public function getTags(){
        $tag_ids = ExerciseTags::find()
            ->select('tag_id')
            ->where(['exec_id' => $this->id])
            ->column();
        return ContentRepo::renderTags($tag_ids);
    }
}
