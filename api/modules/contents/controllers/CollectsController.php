<?php

namespace api\modules\contents\controllers;
use common\components\ResponseCode;
use common\models\UserCollects;
use common\models\BibleVerses;
use Yii;

class CollectsController extends \api\components\ClientController
{
    /**
     * 我的收藏
     */
    public function actionMy(){
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        $collects = UserCollects::find()
            ->where(['uid' => $this->_user()->id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        $result = [];
        /**
         * @var $collect UserCollects
         */
        foreach($collects as $collect){
            $verse = BibleVerses::findOne([
                'book_id' => $collect->book_id,
                'chapter_num' => $collect->chapter_num,
                'verse_num' => $collect->verse_num
            ]);
            
            if($verse){
                $result[] = [
                    'id' => $collect->id,
                    'book_id' => $collect->book_id,
                    'chapter' => $collect->chapter_num,
                    'verse' => $collect->verse_num,
                    'content' => $verse->content,
                    'created_at' => $collect->created_at
                ];
            }
        }

        return $this->renderJSON($result);
    }

    /**
     * 添加收藏
     */
    public function actionAdd(){
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        $book_id = Yii::$app->request->post('book_id');
        $chapter = Yii::$app->request->post('chapter');
        $verse = Yii::$app->request->post('verse');

        if(!$book_id || !$chapter || !$verse){
            return $this->renderJSON([],"参数错误",ResponseCode::INPUT_ERROR);
        }

        // 检查经文是否存在
        $verseModel = BibleVerses::findOne([
            'book_id' => $book_id,
            'chapter_num' => $chapter,
            'verse_num' => $verse
        ]);

        if(!$verseModel){
            return $this->renderJSON([],"经文不存在",ResponseCode::INPUT_ERROR);
        }

        // 检查是否已收藏
        $exist = UserCollects::findOne([
            'uid' => $this->_user()->id,
            'book_id' => $book_id,
            'chapter_num' => $chapter,
            'verse_num' => $verse
        ]);

        if($exist){
            return $this->renderJSON([],"已经收藏过了",ResponseCode::DATA_INVALID);
        }

        $collect = new UserCollects();
        $collect->uid = $this->_user()->id;
        $collect->book_id = $book_id;
        $collect->chapter_num = $chapter;
        $collect->verse_num = $verse;
        $collect->created_at = time();

        if($collect->save()){
            return $this->renderJSON(['id' => $collect->id]);
        }

        return $this->renderJSON([],"收藏失败",ResponseCode::UNKNOWN_ERROR);
    }

    /**
     * 删除收藏
     */
    public function actionDelete(){
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        $id = Yii::$app->request->post('id');
        
        $collect = UserCollects::findOne([
            'id' => $id,
            'uid' => $this->_user()->id
        ]);

        if(!$collect){
            return $this->renderJSON([],"收藏不存在",ResponseCode::DATA_MISSING);
        }

        if($collect->delete()){
            return $this->renderJSON([]);
        }

        return $this->renderJSON([],"删除失败",ResponseCode::DATA_MISSING);
    }

    /**
     * 检查是否已收藏
     */
    public function actionCheck(){
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        $book_id = Yii::$app->request->get('book_id');
        $chapter = Yii::$app->request->get('chapter');
        $verse = Yii::$app->request->get('verse');

        if(!$book_id || !$chapter || !$verse){
            return $this->renderJSON([],"参数错误",ResponseCode::INPUT_ERROR);
        }

        $exist = UserCollects::findOne([
            'uid' => $this->_user()->id,
            'book_id' => $book_id,
            'chapter_num' => $chapter,
            'verse_num' => $verse
        ]);

        return $this->renderJSON(['is_collected' => $exist ? true : false]);
    }
}