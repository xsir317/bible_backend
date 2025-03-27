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

        $book_id = Yii::$app->request->get('book_id');
        $chapter_id = Yii::$app->request->get('chapter_id');

        $query = UserNotes::find()
            ->where(['uid' => $this->_user()->id]);

        if ($book_id) {
            $query->andWhere(['book_id' => $book_id]);
        }
        if ($chapter_id) {
            $query->andWhere(['chapter_id' => $chapter_id]);
        }

        $notes = $query->orderBy(['created_at' => SORT_DESC])->all();
        $result = [];
        /**
         * @var $note UserNotes
         */
        foreach ($notes as $note) {
            $result[] = [
                'id' => $note->id,
                'book_id' => $note->book_id,
                'chapter_id' => $note->chapter_num,
                'verse_id' => $note->verse_num,
                'content' => json_decode($note->content_txt, true),
                'created_at' => $note->created_at
            ];
        }

        return $this->renderJSON($result);
    }

    /**
     * 获取单个笔记详情
     */
    public function actionDetail()
    {
        if (!$this->_user()) {
            return $this->renderJSON([], "没有登录", ResponseCode::NOT_LOGIN);
        }

        $id = Yii::$app->request->get('id');
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
            'chapter_id' => $note->chapter_num,
            'verse_id' => $note->verse_num,
            'content' => json_decode($note->content_txt, true),
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

        $book_id = Yii::$app->request->post('book_id');
        $chapter_id = Yii::$app->request->post('chapter_id');
        $verse_id = Yii::$app->request->post('verse_id');
        $content = Yii::$app->request->post('content');

        if (!$book_id || !$chapter_id || !$verse_id || !$content) {
            return $this->renderJSON([], "参数错误", ResponseCode::INPUT_ERROR);
        }

        // 查找是否已存在笔记
        $note = UserNotes::findOne([
            'uid' => $this->_user()->id,
            'book_id' => $book_id,
            'chapter_id' => $chapter_id,
            'verse_id' => $verse_id
        ]);

        if (!$note) {
            $note = new UserNotes();
            $note->uid = $this->_user()->id;
            $note->book_id = $book_id;
            $note->chapter_num = $chapter_id;
            $note->verse_num = $verse_id;
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

        $id = Yii::$app->request->post('id');
        
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