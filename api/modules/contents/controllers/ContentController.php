<?php

namespace api\modules\contents\controllers;

use api\components\ClientController;
use common\components\ResponseCode;
use common\models\BibleVerses;
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
        return $this->renderJSON([
            'verses' => $verses
        ]);
    }
}