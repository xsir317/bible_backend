<?php

namespace api\modules\contents\controllers;

use common\models\UserNotes;
use common\components\ResponseCode;
use Yii;

class NotesController extends \api\components\ClientController
{
    /**
     * 获取笔记列表
     */
    public function actionList()
    {
        if (!$this->_user()) {
            return $this->renderJSON([], "没有登录", ResponseCode::NOT_LOGIN);
        }
        $page = $this->get('page',1);
        $pageSize = 20;

        $book_id = $this->get('book_id');
        $chapter_num = $this->get('chapter_num');

        $query = UserNotes::find()
            ->where(['uid' => $this->_user()->id]);

        if ($book_id) {
            $query->andWhere(['book_id' => $book_id]);
        }
        if ($chapter_num) {
            $query->andWhere(['chapter_num' => $chapter_num]);
        }

        $notes = $query
            ->orderBy(['id' => SORT_DESC])
            ->limit($pageSize+1)
            ->offset($pageSize * ($page - 1))
            ->all();
        $has_next = 0;
        if(count($notes) > $pageSize){
            $has_next = 1;
            array_pop($notes);
        }
        $result = [];
        /**
         * @var $note UserNotes
         */
        foreach ($notes as $note) {
            $result[] = [
                'id' => $note->id,
                'book_id' => $note->book_id,
                'chapter_num' => $note->chapter_num,
                'verse_num' => $note->verse_num,
                'content' => $note->getContent(),
                'created_at' => $note->created_at
            ];
        }

        return $this->renderJSON([
            'list' => $result,
            'has_next' => $has_next
        ]);
    }

    /**
     * 获取单个笔记详情
     */
    public function actionDetail()
    {
        if (!$this->_user()) {
            return $this->renderJSON([], "没有登录", ResponseCode::NOT_LOGIN);
        }

        $id = $this->get('id');
        $note = UserNotes::findOne([
            'id' => $id,
            'uid' => $this->_user()->id
        ]);

        if (!$note) {
            return $this->renderJSON([], "笔记不存在", ResponseCode::INPUT_ERROR);
        }

        return $this->renderJSON([
            'id' => $note->id,
            'book_id' => $note->book_id,
            'chapter_num' => $note->chapter_num,
            'verse_num' => $note->verse_num,
            'content' => $note->getContent(),
            'created_at' => $note->created_at
        ]);
    }

    /**
     * 添加或更新笔记
     */
    public function actionSave()
    {
        if (!$this->_user()) {
            return $this->renderJSON([], "没有登录", ResponseCode::NOT_LOGIN);
        }

        $book_id = $this->get('book_id');
        $chapter_num = $this->get('chapter_num');
        $verse_num = $this->get('verse_num');
        $content = $this->get('content');

        if (!$book_id || !$chapter_num || !$verse_num || !$content) {
            return $this->renderJSON([], "参数错误", ResponseCode::INPUT_ERROR);
        }

        // 查找是否已存在笔记
        $note = UserNotes::findOne([
            'uid' => $this->_user()->id,
            'book_id' => $book_id,
            'chapter_num' => $chapter_num,
            'verse_num' => $verse_num
        ]);

        if (!$note) {
            $note = new UserNotes();
            $note->uid = $this->_user()->id;
            $note->book_id = $book_id;
            $note->chapter_num = $chapter_num;
            $note->verse_num = $verse_num;
            $note->created_at = date('Y-m-d H:i:s');
        }

        $note->content_txt = json_encode($content);

        if ($note->save()) {
            return $this->renderJSON(['id' => $note->id]);
        }

        return $this->renderJSON([], "保存失败", ResponseCode::UNKNOWN_ERROR);
    }

    /**
     * 删除笔记
     */
    public function actionDelete()
    {
        if (!$this->_user()) {
            return $this->renderJSON([], "没有登录", ResponseCode::NOT_LOGIN);
        }

        $id = $this->get('id');
        
        $note = UserNotes::findOne([
            'id' => $id,
            'uid' => $this->_user()->id
        ]);

        if (!$note) {
            return $this->renderJSON([], "笔记不存在", ResponseCode::DATA_INVALID);
        }

        if ($note->delete()) {
            return $this->renderJSON([]);
        }

        return $this->renderJSON([], "删除失败", ResponseCode::UNKNOWN_ERROR);
    }
}