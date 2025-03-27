<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "bible_verses".
 *
 * @property int $id
 * @property string $version 版本标识
 * @property int $book_id 书卷ID
 * @property int $chapter_num 章号
 * @property int $verse_num 节号
 * @property string $content 经文内容
 *
 * @property BibleExplanations[] $bibleExplanations
 */
class BibleVerses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bible_verses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['version', 'book_id', 'chapter_num', 'verse_num', 'content'], 'required'],
            [['book_id', 'chapter_num', 'verse_num'], 'integer'],
            [['version'], 'string', 'max' => 10],
            [['content'], 'string', 'max' => 1024],
            [['version', 'book_id', 'chapter_num', 'verse_num'], 'unique', 'targetAttribute' => ['version', 'book_id', 'chapter_num', 'verse_num']],
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
            'verse_num' => 'Verse Num',
            'content' => 'Content',
        ];
    }
}
