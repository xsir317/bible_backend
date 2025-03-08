<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '/';

    public $css = [
        //'/admin/vendors/font-awesome/css/font-awesome.min.css',
        'css/custom.css',
    ];
    public $js = [
        'js/custom.js?v=20220615',
        'https://asset.v.show/js/fontawesome.5.15.1.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'yii\widgets\PjaxAsset',
        //'backend\assets\BootstrapAsset',
    ];
}
