<?php

use backend\widgets\Panel;

/* @var $this yii\web\View */
/* @var $model \common\models\AdminUsers */

$this->title = '编辑管理员账号';
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
?>

<?php Panel::begin(['header'=>'','headerMenu'=> ['添加'=>'测试'],'breadcrumbs'=>$this->params['breadcrumbs']]); ?>
<?= $this->render('_form', [
    'model' => $model,
]) ?>
<?php Panel::end(); ?>
