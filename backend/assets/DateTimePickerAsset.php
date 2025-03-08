<?php
/**
 * Created by PhpStorm.
 * User: candy
 * Date: 2018/1/15
 * Time: 10:58
 */

namespace backend\assets;


use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

class DateTimePickerAsset extends AssetBundle
{


    public $sourcePath = "@media";

    public $css = [
        'plugins/datetimepicker/jquery.datetimepicker.css'
    ];

    public $jsOptions = [
//        'position' => View::POS_HEAD
    ];

    public $js = [
        'plugins/datetimepicker/build/jquery.datetimepicker.full.js'
    ];
    public $depends = [
        YiiAsset::class
    ];

}