<?php

namespace common\components\llm\adapters;

abstract class Adapter
{
    /**
     * @param $prompt
     * @param $params
     * @return array
     */
    public function execute($prompt, $params){}
}