<?php

namespace common\components\llm\adapters;

abstract class Adapter
{
    /**
     * @param $prompt
     * @param $params
     * @return array
     */
    abstract public function execute($prompt, $params , $model);
}