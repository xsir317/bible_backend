<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_notes".
 *
 * @property int $id
 * @property int $uid
 * @property int $book_id
 * @property int $chapter_id
 * @property int $verse_id
 * @property string $content_txt 笔记文字，暂时设置为json
 * @property string $created_at
 */
class UserNotes extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_notes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'book_id', 'chapter_id', 'verse_id', 'content_txt'], 'required'],
            [['uid', 'book_id', 'chapter_id', 'verse_id'], 'integer'],
            [['content_txt'], 'string'],
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
            'uid' => 'Uid',
            'book_id' => 'Book ID',
            'chapter_id' => 'Chapter ID',
            'verse_id' => 'Verse ID',
            'content_txt' => 'Content Txt',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 获取用户写的笔记的map
     * [
     *   verse_id => 内容
     * ]
     * @param $uid
     * @param $book_id
     * @param $chapter_id
     * @return array
     */
    public static function getUserNotes($uid , $book_id , $chapter_id ){
        $ret = [];
        $q = self::find()
            ->where(['uid' => $uid,'book_id' => $book_id,'chapter_id' => $chapter_id])
            ->all();
        foreach ($q as $row){
            $ret[$row->verse_id] = json_decode($row->content_txt ,1 );
        }

        return $ret;
    }
}
