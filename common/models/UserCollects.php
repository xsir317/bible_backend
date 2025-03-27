<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_collects".
 *
 * @property int $id
 * @property int $uid
 * @property int $book_id
 * @property int $chapter_id
 * @property int $verse_id
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
            [['uid', 'book_id', 'chapter_id', 'verse_id'], 'required'],
            [['uid', 'book_id', 'chapter_id', 'verse_id'], 'integer'],
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
            'created_at' => 'Created At',
        ];
    }

    public static function getUserCollects($uid , $book_id , $chapter_id){
        return self::find()
            ->select('verse_id')
            ->where(['uid' => $uid,'book_id' => $book_id,'chapter_id' => $chapter_id])
            ->column();
    }

}
