<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace backend\widgets\grid2;

use yii\helpers\Html;

class GridView extends \kartik\grid\GridView
{
    public $panel = ['type'=>'default', 'heading'=>'',];
    public $export = ['showConfirmAlert'=>'false'];
    public $exportConfig = [
        'csv' => [],
        'xls' => [],
    ];
    public $responsiveWrap = false;


}
