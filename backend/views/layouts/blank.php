<?php

/** @var \yii\web\View $this */
/** @var string $content */


use frontend\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>" class="h-100">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta name="renderer" content="webkit">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
        <link rel="stylesheet" href="/css/goban.css" />
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= \yii\helpers\Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layui-this"><?= $this->title ?></li>
            </ul>
            <?php if(!Yii::$app->user->isGuest) :?>
                <ul class="layui-nav layui-layout-right">
                    <li class="layui-nav-item "><a href="/site/logout">登出</a></li>
                </ul>
            <?php endif;?>
        </div>
        <div class="layui-body">
            <div style="padding: 15px;">
                <?= $content ?>
            </div>
        </div>
        <div class="layui-footer">
            -
        </div>
    </div>
    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage();
