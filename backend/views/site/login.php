<?php
use yii\helpers\Html;

$this->title = '登录授权中心';
?>
<div class="layui-main">
    <fieldset class="layui-elem-field layui-field-title" style="margin-top: 30px;">
        <legend><?= $this->title ?></legend>
    </fieldset>
    <?php if($err):?>
        <blockquote class="layui-elem-quote">
            <?= $err ?>
        </blockquote>
    <?php endif;?>
    <?= Html::beginForm('/site/login','post' ,['class' => 'layui-form']) ?>
    <div class="layui-form-item">
        <label class="layui-form-label">手机号</label>
        <div class="layui-input-inline">
            <?= Html::textInput('mobile' ,'',['class' => 'layui-input']) ?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">密码</label>
        <div class="layui-input-inline">
            <?= Html::passwordInput('password' ,'',['class' => 'layui-input']) ?>
        </div>
    </div>
    <div class="layui-form-item">
        <label class="layui-form-label">验证器码</label>
        <div class="layui-input-inline">
            <?= Html::textInput('ga_number' ,'',['class' => 'layui-input']) ?>
        </div>
    </div>
    <div class="layui-form-item">
        <?= Html::submitButton('Login', ['class' => 'layui-btn layui-btn-normal', 'name' => 'login-button']) ?>
    </div>
    <?= Html::endForm(); ?>
</div>
