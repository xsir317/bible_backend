<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bible_passages".
 *
 * @property int $id
 * @property string $version
 * @property int $book_id
 * @property int $chapter_num
 * @property int $start_verse
 * @property int $end_verse
 * @property int $token_count 估算的token长度
 *
 * @property BibleExplanations[] $bibleExplanations
 */
class BiblePassages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bible_passages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['version', 'book_id', 'chapter_num', 'start_verse', 'end_verse', 'token_count'], 'required'],
            [['book_id', 'chapter_num', 'start_verse', 'end_verse', 'token_count'], 'integer'],
            [['version'], 'string', 'max' => 10],
            [['version', 'book_id', 'chapter_num', 'start_verse', 'end_verse'], 'unique', 'targetAttribute' => ['version', 'book_id', 'chapter_num', 'start_verse', 'end_verse']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'book_id' => 'Book ID',
            'chapter_num' => 'Chapter Num',
            'start_verse' => 'Start Verse',
            'end_verse' => 'End Verse',
            'token_count' => 'Token Count',
        ];
    }

    /**
     * Gets query for [[BibleExplanations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBibleExplanations()
    {
        return $this->hasMany(BibleExplanations::class, ['passage_id' => 'id']);
    }
}
