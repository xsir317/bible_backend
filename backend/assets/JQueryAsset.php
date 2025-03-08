<?php
/**
 * Created by PhpStorm.
 * User: candy
 * Date: 2018/1/15
 * Time: 10:28
 */

namespace backend\assets;


use yii\web\AssetBundle;
use yii\web\View;

class JQueryAsset extends AssetBundle
{

    public $sourcePath = "@media";

    public $css = [];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
    public $js = [
        '/js/jquery.min.js',
    ];
    public $depends = [];

}