<?php

namespace backend\assets;


use yii\web\AssetBundle;

class LayuiAsset extends AssetBundle
{
    public $css = [
        '/layui/css/layui.css'
    ];

    public $js = [
        '/layui/layui.js',
        '/js/load.js',// 解决老版本冲突
    ];

    public $depends = [
        'backend\assets\JQueryAsset'
    ];
}