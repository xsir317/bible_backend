<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_notes".
 *
 * @property int $id
 * @property int $uid
 * @property int $book_id
 * @property int $chapter_num
 * @property int $verse_num
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
            [['uid', 'book_id', 'chapter_num', 'verse_num', 'content_txt'], 'required'],
            [['uid', 'book_id', 'chapter_num', 'verse_num'], 'integer'],
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
            'chapter_num' => 'Chapter ID',
            'verse_num' => 'Verse ID',
            'content_txt' => 'Content Txt',
            'created_at' => 'Created At',
        ];
    }

    /**
     * 获取用户写的笔记的map
     * [
     *   verse_num => 内容
     * ]
     * @param $uid
     * @param $book_id
     * @param $chapter_num
     * @return array
     */
    public static function getUserNotes($uid , $book_id , $chapter_num ){
        $ret = [];
        $q = self::find()
            ->where(['uid' => $uid,'book_id' => $book_id,'chapter_num' => $chapter_num])
            ->all();
        foreach ($q as $row){
            $ret[$row->verse_num] = $row->getContent();
        }

        return $ret;
    }

    public function getContent(){
        return json_decode($this->content_txt ,1 );
    }
}
