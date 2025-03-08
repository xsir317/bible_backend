<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bible_explanations".
 *
 * @property int $verse_id
 * @property string $lang 语言代码
 * @property string $model 模型标识
 * @property string $content
 * @property string|null $updated_at
 *
 * @property BibleVerses $verse
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
            [['verse_id', 'lang', 'model', 'content'], 'required'],
            [['verse_id'], 'integer'],
            [['content'], 'string'],
            [['updated_at'], 'safe'],
            [['lang'], 'string', 'max' => 5],
            [['model'], 'string', 'max' => 50],
            [['verse_id', 'lang', 'model'], 'unique', 'targetAttribute' => ['verse_id', 'lang', 'model']],
            [['verse_id'], 'exist', 'skipOnError' => true, 'targetClass' => BibleVerses::class, 'targetAttribute' => ['verse_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'verse_id' => 'Verse ID',
            'lang' => 'Lang',
            'model' => 'Model',
            'content' => 'Content',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Verse]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVerse()
    {
        return $this->hasOne(BibleVerses::class, ['id' => 'verse_id']);
    }
}
