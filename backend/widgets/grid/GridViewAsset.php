<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */

namespace backend\widgets\grid;

class GridViewAsset extends \yii\web\AssetBundle
{
    public $baseUrl = '@backend';
    public $sourcePath = '@webroot/admin/vendors/datatables.net-bs/css';
    public $css = [
        'dataTables.bootstrap.min.css',
    ];
    public $js = [];
}
