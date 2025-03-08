<?php

use backend\widgets\Panel;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model \common\models\oauth\OauthUsers */
/* @var $apps array */

$this->title = '用户角色分配' . $model->name ;
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
?>

<?php Panel::begin(['header'=>'','headerMenu'=> ['添加'=>'测试'],'breadcrumbs'=>$this->params['breadcrumbs']]); ?>
<?= Html::beginForm() ?>
<?php foreach ($apps as $app):?>
<div class="form-group">
    <h3><?= $app->name ?></h3>
    <?php if(!empty($all_roles[$app->id])):?>
    <div class="form-inline">
        <?php foreach ($all_roles as $role):?>
            <?php if($role->app_id == $app->id):?>
            <label class="<?= $role->full_control ? 'full_control':'' ?>"><?= Html::checkbox('role[]' , isset($my_roles[$role->id]) , ['value' => $role->id]) ?> <?= $role->name ?></label>
            <?php endif;?>
        <?php endforeach;?>
    </div>
    <?php endif;?>
</div>
<?php endforeach;?>
<div class="form-group">
    <?= Html::submitButton( 'Update', ['class' => 'btn btn-primary' , 'id' => 'user-role-submit']) ?>
</div>
<?= Html::endForm();?>
<?php Panel::end(); ?>
<style>
    .full_control{
        color:#C94343;
        font-weight: bold;
    }
</style>