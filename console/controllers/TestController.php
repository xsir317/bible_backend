<?php

namespace console\controllers;

use common\components\llm\LLMService;
use common\models\UserReadingProgress;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionTest(){
        $readed1 = UserReadingProgress::doRead(1,1,1);
        $readed2 = UserReadingProgress::findOne(4);
        var_dump($readed1->getChapterMap());
        var_dump($readed2->getChapterMap());
    }
}