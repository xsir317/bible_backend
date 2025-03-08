<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var \common\models\Users $model
 * @var \common\models\UserDetails $detail
 */
?>
<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <?= $form->errorSummary($model) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>
    <?= $form->field($detail, 'realname')->textInput(['maxlength' => true]) ?>
    <?= $form->field($detail, 'contact')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'grade')->dropDownList( \common\repository\UserRepo::getAvailableLevels() ) ?>
    <?= $form->field($detail, 'contact_backup')->textInput(['maxlength' => true]) ?>
    <?= $form->field($detail, 'comment')->textInput(['maxlength' => true]) ?>
    <?= $form->field($detail, 'client_level')->dropDownList(\common\models\UserDetails::USER_LEVELS) ?>
    <?= $form->field($detail, 'in_charge')->dropDownList($admins) ?>

    <div class="form-group">
        <?= Html::submitButton( '修改', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
