<?php

namespace common\models;

use common\components\DistributedLock;
use Yii;

/**
 * This is the model class for table "user_reading_progress".
 *
 * @property int $id
 * @property int $uid 用户ID
 * @property int $book_id 书卷ID(如创世记=1)
 * @property int $last_chapter_id 最后阅读章节
 * @property int $max_chapter_id 最大已读章节
 * @property resource $chapter_bitmap 章节位图(150章需19字节)
 * @property string $updated_at 更新时间
 * @property string $created_at
 */
class UserReadingProgress extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_reading_progress';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['max_chapter_id'], 'default', 'value' => 0],
            [['chapter_bitmap'], 'default', 'value' => ' '],
            [['uid', 'book_id'], 'required'],
            [['uid', 'book_id', 'last_chapter_id', 'max_chapter_id'], 'integer'],
            [['updated_at', 'created_at'], 'safe'],
            [['chapter_bitmap'], 'string', 'max' => 20],
            [['uid', 'book_id'], 'unique', 'targetAttribute' => ['uid', 'book_id']],
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
            'last_chapter_id' => 'Last Chapter ID',
            'max_chapter_id' => 'Max Chapter ID',
            'chapter_bitmap' => 'Chapter Bitmap',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
        ];
    }

    private $chapter_map = null;
    private function init_chapter_map(){
        if($this->chapter_map === null){
            $this->chapter_map = [];
            for($i = 0; $i < 20 ; $i++){
                if($i +1 > strlen($this->chapter_bitmap)){
                    break;
                }
                $bitmap = ord($this->chapter_bitmap[$i]);
                //$bitmap = $this->chapter_bitmap[$i];
                for($j = 0 ; $j < 8 ; $j++){
                    if( $bitmap & (1 << $j) ){
                        //这里为了方便，我们统一挪一位
                        $this->chapter_map[ $i * 8 + $j + 1 ] = 1;
                    }
                }
            }
        }
        return $this->chapter_map;
    }

    /**
     * 获取阅读过的章节
     * @return array
     */
    public function getChapterMap(){
        return $this->init_chapter_map();
    }

    /**
     * 获取章节的阅读状况
     * @param $uid
     * @param $book_id
     * @return array
     */
    public static function getReadChapters($uid , $book_id){
        $return = [
            'last_chapter_id' => 0,
            'chapter_map' => [],
        ];
        $log = self::find()
            ->where(['uid' => $uid , 'book_id' => $book_id])
            ->one();
        if($log){
            $return['last_chapter_id'] = $log->last_chapter_id;
            $return['chapter_map'] = $log->getChapterMap();
        }
        return $return;
    }

    /**
     * 获取用户阅读的历史,每book 返回时间和最后章节即可
     * @param $uid
     * @return array
     */
    public static function getReadBooks($uid){
        return self::find()
            ->select(['book_id', 'last_chapter_id', 'updated_at',])
            ->where(['uid' => $uid])
            ->orderBy('updated_at desc')
            ->asArray()
            ->all();
    }

    /**
     * 记录阅读章节
     * @param $uid
     * @param $book_id
     * @param $chapter_id
     * @param $time
     * @return array|UserReadingProgress|mixed|\yii\db\ActiveRecord|null
     * @throws \yii\db\Exception
     */
    public static function doRead($uid , $book_id , $chapter_id , $time = null){
        //lock
        if(!DistributedLock::getLock('doRead:' . $uid , 1)){
            return false;
        }
        if(!$time){
            $time = date('Y-m-d H:i:s');
        }
        $log = self::find()
            ->where(['uid' => $uid , 'book_id' => $book_id])
            ->one();
        if(!$log){
            $log = new self();
            $log->uid = $uid;
            $log->book_id = $book_id;
            $log->created_at = $time;
        }
        $log->updated_at = $time;
        $log->setReadChapter($chapter_id);
        $log->save();
        return $log;
    }

    private function setReadChapter($chapter_num){
        $this->chapter_map = null;
        $bitIndex = $chapter_num - 1;
        $bytePos = (int)($bitIndex / 8);
        $bitPos = $bitIndex % 8;

        $tmp_bitmap = $this->chapter_bitmap;
        while ($bytePos >= strlen($tmp_bitmap)) {
            $tmp_bitmap .= "\x00";
        }
        $byte = ord($tmp_bitmap[$bytePos]);
        $byte |= (1 << $bitPos);
        $tmp_bitmap[$bytePos] = chr($byte);
        $this->chapter_bitmap = $tmp_bitmap;
        $this->max_chapter_id = max($chapter_num , $this->max_chapter_id);
        $this->last_chapter_id = $chapter_num;
    }
}
