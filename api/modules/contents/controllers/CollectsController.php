<?php

namespace api\modules\contents\controllers;
use common\components\DistributedLock;
use common\components\ResponseCode;
use common\models\UserCollects;
use common\models\BibleVerses;
use common\repository\ContentRepo;
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
        $version = 'CUV';
        $page = $this->get('page',1);

        $book_id = $this->get('book_id');
        $chapter_num = $this->get('chapter_num');
        $pageSize = 20;

        $query = UserCollects::find()
            ->where(['uid' => $this->_user()->id]);

        if ($book_id) {
            $query->andWhere(['book_id' => $book_id]);
        }
        if ($chapter_num) {
            $query->andWhere(['chapter_num' => $chapter_num]);
        }
        $collects = $query->orderBy(['id' => SORT_DESC])
            ->limit($pageSize+1)
            ->offset($pageSize * ($page - 1))
            ->all();
        $has_next = 0;
        if(count($collects) > $pageSize){
            $has_next = 1;
            array_pop($collects);
        }

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
                    'book_name' => ContentRepo::BOOKS[$version][$collect->book_id]['name'] ?? '',
                    'chapter' => $collect->chapter_num,
                    'verse' => $collect->verse_num,
                    'content' => $verse->content,
                    'created_at' => $collect->created_at
                ];
            }
        }

        return $this->renderJSON([
            'list' => $result,
            'has_next' => $has_next
        ]);
    }

    /**
     * 添加收藏
     */
    public function actionAdd(){
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        $book_id = $this->get('book_id');
        $chapter = $this->get('chapter');
        $verse = $this->get('verse');

        if(!$book_id || !$chapter || !$verse){
            return $this->renderJSON([],"参数错误",ResponseCode::INPUT_ERROR);
        }
        if(!DistributedLock::getLock("collect:".$this->_user()->id)){
            return $this->renderJSON();
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
        $collect->created_at = date('Y-m-d H:i:s');

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

        $book_id = $this->get('book_id');
        $chapter = $this->get('chapter');
        $verse = $this->get('verse');

        if(!$book_id || !$chapter || !$verse){
            return $this->renderJSON([],"参数错误",ResponseCode::INPUT_ERROR);
        }
        if(!DistributedLock::getLock("del_collect:".$this->_user()->id)){
            return $this->renderJSON();
        }

        // 检查是否已收藏
        $exist = UserCollects::findOne([
            'uid' => $this->_user()->id,
            'book_id' => $book_id,
            'chapter_num' => $chapter,
            'verse_num' => $verse
        ]);

        if(!$exist){
            return $this->renderJSON([],"收藏不存在",ResponseCode::DATA_MISSING);
        }

        if($exist->delete()){
            return $this->renderJSON([]);
        }

        return $this->renderJSON([],"删除失败",ResponseCode::DATA_MISSING);
    }
}
