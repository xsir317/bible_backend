<?php

namespace common\models;

use common\repository\ContentRepo;
use Yii;

/**
 * This is the model class for table "reviews".
 *
 * @property int $id
 * @property int $uid
 * @property int $admin_id
 * @property string $title
 * @property string $content
 * @property string $attr_change
 * @property string $relate_videos
 * @property string $relate_execs
 * @property int $read_at
 * @property string $created_at
 */
class Reviews extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'reviews';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'admin_id', 'title', 'content', 'relate_videos', 'relate_execs'], 'required'],
            [['uid', 'admin_id', 'read_at'], 'integer'],
            [['content'], 'string'],
            [['created_at'], 'safe'],
            [['title'], 'string', 'max' => 128],
            [['attr_change'], 'string', 'max' => 1024],
            [['relate_videos', 'relate_execs'], 'string', 'max' => 255],
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
            'admin_id' => 'Admin ID',
            'title' => '标题',
            'content' => '正文',
            'attr_change' => 'Attr Change',
            'relate_videos' => '关联Videos',
            'relate_execs' => '关联习题',
            'read_at' => 'Read At',
            'created_at' => 'Created At',
        ];
    }

    public function getUser(){
        return $this->uid ? Users::findOne($this->uid) : null;
    }

    public function getAdmin(){
        return $this->admin_id;
    }

    public function getShowAttrChange(){
        $decoded = @json_decode($this->attr_change,1);
        foreach ($decoded as $k=>&$v){
            $v['txt'] = UserAttributes::ATTR_TEXT[$v['id']] ?? '';
        }
        return $decoded;
    }

    public function getVideos(){
        $video_ids = [];
        foreach (explode(',',$this->relate_videos) as $item){
            if(intval($item)){
                $video_ids[] = intval($item);
            }
        }
        return empty($video_ids) ? [] : ContentRepo::renderVideos(
            Videos::find()->where(['id' => $video_ids])->all()
        );
    }

    public function getExecs(){
        $exec_ids = [];
        foreach (explode(',',$this->relate_execs) as $item){
            if(intval($item)){
                $exec_ids[] = intval($item);
            }
        }
        return empty($exec_ids) ? [] : ContentRepo::renderExecs(
            Exercise::find()->where(['id' => $exec_ids])->all()
        );
    }
}
