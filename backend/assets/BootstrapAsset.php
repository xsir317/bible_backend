<?php
/**
 * Created by PhpStorm.
 * User: candy
 * Date: 2018/1/15
 * Time: 10:32
 */

namespace backend\assets;


use yii\web\AssetBundle;
use yii\web\View;

class BootstrapAsset extends AssetBundle
{

    public $sourcePath = "@media";

    public $css = [
        'plugins/bootstrap-3.3.7/css/bootstrap.css',
        'plugins/bootstrap-3.3.7/css/bootstrap-theme.css'
    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
    public $js = [
        'plugins/bootstrap-3.3.7/js/bootstrap.js'
    ];
    public $depends = [];

}