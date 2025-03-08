<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \common\models\AdminUsers  */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apps-form">

    <?php $form = ActiveForm::begin(['id' => 'apps-form']); ?>
    <?= $form->field($model, 'name')->textInput() ?>
    <?= $form->field($model, 'mobile')->textInput() ?>
    <?php $pwd_input = $form->field($model, 'password')->passwordInput(['value' => '']);
    if(!$model->isNewRecord){
        $pwd_input->enableClientValidation = false;
    }
    echo $pwd_input;
    ?>
    <div class="form-group">
        <?= Html::a($model->isNewRecord ? 'Create' : 'Update', '#' , ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary' , 'id' => 'apps-form-submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
$('#apps-form-submit').click(function(){
    $.post('<?= Yii::$app->request->getUrl() ?>' , $('#apps-form').serialize() , function(_data){
        if(_data.msg){
            layer.alert(_data.msg ,{yes: function(index){
                layer.close(index)
                if(_data.code == 200){
                    window.location.href='/admins/index';
                }
            }});
        }
    } , 'json');
});
</script>