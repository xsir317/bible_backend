<?php

namespace console\controllers;

use common\components\llm\LLMService;
use yii\console\Controller;

class TestController extends Controller
{
    public function actionTest(){
        $llm = new LLMService();
        $response = $llm->generate(LLMService::MODEL_ALI_QWEN , '请给我讲解如下经文： 神说，我们要照着我们的形像，按着我们的样式造人，使他们管理海里的鱼，空中的鸟，地上的牲畜，和全地，并地上所爬的一切昆虫。');
        var_dump($response);
    }
}