<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_collects".
 *
 * @property int $id
 * @property int $uid
 * @property int $book_id
 * @property int $chapter_num
 * @property int $verse_num
 * @property string $created_at
 */
class UserCollects extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_collects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'book_id', 'chapter_num', 'verse_num'], 'required'],
            [['uid', 'book_id', 'chapter_num', 'verse_num'], 'integer'],
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
            'created_at' => 'Created At',
        ];
    }

    public static function getUserCollects($uid , $book_id , $chapter_num){
        return self::find()
            ->select('verse_num')
            ->where(['uid' => $uid,'book_id' => $book_id,'chapter_num' => $chapter_num])
            ->column();
    }

}
