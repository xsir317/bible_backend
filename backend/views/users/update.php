<?php

use yii\helpers\Html;
use backend\widgets\Panel;

/* @var $this yii\web\View */
/* @var $model common\models\Users */

$this->title = '用户个人信息修改';
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];

$header = '修改用户';
?>

<?php Panel::begin(['header' => $header . ' : ' . $model->nickname, 'headerMenu' => ['添加' => '测试'], 'breadcrumbs' => $this->params['breadcrumbs']]); ?>
<?= $this->render('_form', [
    'model' => $model,
    'detail' => $detail,
    'attributes' => $attributes,
    'admins' => $admins
]) ?>
<?php Panel::end(); ?>
