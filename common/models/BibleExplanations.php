<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bible_explanations".
 *
 * @property int $id
 * @property int $passage_id 关联段落ID
 * @property string $lang 语言代码(zh-CN/en等)
 * @property string $model 模型标识(gpt-4等)
 * @property string $content 解释内容
 * @property string|null $context_verses 自动生成章节范围
 * @property string|null $updated_at
 *
 * @property BiblePassages $passage
 */
class BibleExplanations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bible_explanations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['passage_id', 'lang', 'model', 'content'], 'required'],
            [['passage_id'], 'integer'],
            [['content'], 'string'],
            [['updated_at'], 'safe'],
            [['lang'], 'string', 'max' => 5],
            [['model'], 'string', 'max' => 50],
            [['context_verses'], 'string', 'max' => 255],
            [['passage_id', 'lang', 'model'], 'unique', 'targetAttribute' => ['passage_id', 'lang', 'model']],
            [['passage_id'], 'exist', 'skipOnError' => true, 'targetClass' => BiblePassages::class, 'targetAttribute' => ['passage_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'passage_id' => 'Passage ID',
            'lang' => 'Lang',
            'model' => 'Model',
            'content' => 'Content',
            'context_verses' => 'Context Verses',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Passage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPassage()
    {
        return $this->hasOne(BiblePassages::class, ['id' => 'passage_id']);
    }
}
