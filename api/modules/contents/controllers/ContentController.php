<?php

namespace api\modules\contents\controllers;

use api\components\ClientController;
use common\components\ResponseCode;
use common\models\BibleExplanations;
use common\models\BiblePassages;
use common\models\BibleVerses;
use common\models\UserReadingProgress;
use common\repository\ContentRepo;

/**
 *
 */
class ContentController extends ClientController
{
    public function actionVersions(){
        //获取圣经版本，测试阶段就一个
    }

    public function actionBooksMenu(){
        $version = 'CUV';//TODO 以后得支持选择
        if(!isset(ContentRepo::BOOKS[$version])){
            return $this->renderJSON([] ,'not found' , ResponseCode::DATA_MISSING);
        }
        return $this->renderJSON(ContentRepo::BOOKS[$version]);
    }

    public function actionChapterContent(){
        $version = 'CUV';//TODO 以后得支持选择
        $book_id = intval($this->get('book_id'));
        $chapter_id = intval($this->get('chapter_id'));
        if(!$version || !$book_id || !$chapter_id){
            return $this->renderJSON([] ,'not found' , ResponseCode::REQUEST_ERROR);
        }

        //TODO 获取数据的放到repo里去
        $verses = BibleVerses::find()
            ->select(['id','verse_num','content'])
            ->where([
                'version' => $version,
                'book_id' => $book_id,
                'chapter_num' => $chapter_id
            ])
            ->asArray()
            ->all();
        //分段
        $explains = BiblePassages::find()
            ->select(['start_verse','end_verse','content as explain_txt'])
            ->leftJoin(['x' => BibleExplanations::tableName()] , 'x.passage_id=bible_passages.id')
            ->where([
                'bible_passages.version' => $version,
                'bible_passages.book_id' => $book_id,
                'bible_passages.chapter_num' => $chapter_id
            ])
            ->asArray()
            ->all();
        //记录阅读
        if($this->_user()){
            UserReadingProgress::doRead($this->_user()->id , $book_id , $chapter_id);
        }
        return $this->renderJSON([
            'verses' => $verses,
            'explains' => $explains
        ]);
    }

    public function actionChaptersRead(){
        $book_id = intval($this->get('book_id'));
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }

        return $this->renderJSON([
            'history' => UserReadingProgress::getReadChapters($this->_user()->id , $book_id)
        ]);
    }

    /**
     * contents/content/reading-history
     * 按日期返回book 名字、 最后阅读时间、 最后阅读的章，方便客户端展示和跳转
     */
    public function actionReadingHistory(){
        $version = 'CUV';//TODO 以后得支持选择
        if(!isset(ContentRepo::BOOKS[$version])){
            return $this->renderJSON([] ,'not found' , ResponseCode::DATA_MISSING);
        }
        if(!$this->_user()){
            return $this->renderJSON([],"没有登录",ResponseCode::NOT_LOGIN);
        }
        $history = UserReadingProgress::getReadBooks($this->_user()->id);

        $return = [];
        foreach ($history as $row){
            $return[] = [
                'updated_at' => $row['updated_at'],
                'book_id' => $row['book_id'],
                'book_name' => ContentRepo::BOOKS[$version][$row['book_id']] ?? '',
                'last_chapter_id' => $row['last_chapter_id'],
            ];
        }

        return $this->renderJSON([
            'history' => $return
        ]);
    }
}