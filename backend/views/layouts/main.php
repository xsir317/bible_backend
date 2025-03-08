<?php

/* @var $this \yii\web\View */

/* @var $content string */

use backend\assets\AppAsset;
use backend\assets\LayuiAsset;
use yii\helpers\Html;
use common\widgets\Alert;

LayuiAsset::register($this);//layui 依赖了 backend 的 jquery
AppAsset::register($this);// App里面有一个 YiiAsset 依赖了Yii 的jquery TODO 理清楚关系然后保留一个。

// url 处理类
$this->registerJsFile('js/url_helper.js', [
    'depends' => [\backend\assets\AppAsset::class]
]);
$this->registerJsFile('/js/g6.min.js');
$menu_id = Yii::$app->request->get('menu_id');
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="/favicon.ico" rel="icon">
    <?php $this->head() ?>
</head>
<body class="nav-md">
<style>
    .side-menu li.active .child_menu {
        display: block;
    }

    .side-menu li.active .child_menu li .child_menu {
        display: none;
    }

    .side-menu li.active .child_menu li.active .child_menu {
        display: block;
    }
</style>
<?php
if ($android = Yii::$app->request->get('android')){
    $android = '&android='.$android;
}
$this->registerJs("
    var links = $('.right_col a');
    $.each(links,function(index, value){
        var url = $(value).attr('href');
        var attr = 'href';
        if($(value).attr('data-href')){
            var url = $(value).attr('data-href');
            var attr = 'data-href';
        }
        if(url && url!='#' && url.indexOf('#') ===-1 ){
            if(url.indexOf('?') == -1){
                $(value).attr(attr,url+'?menu_id='+" . Yii::$app->request->get('menu_id', 0) . "+'".$android."')
            }else{
                $(value).attr(attr,url+'&menu_id='+" . Yii::$app->request->get('menu_id', 0) . "+'".$android."')
            }
        }
    })
");
?>
<div class="container body">
    <?php $this->beginBody(); ?>
    <div class="main_container">
        <div class="col-md-3 left_col">
            <div class="left_col scroll-view">
                <div class="navbar nav_title" style="border: 0;">
                    <a href="/" class="site_title">
                        <i class="fa fa-home"></i>
                        <span><?= Yii::$app->params['siteName'] ?></span>
                    </a>
                </div>

                <div class="clearfix"></div>

                <!-- sidebar menu -->
                <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
                    <div class="menu_section">
                        <?= \backend\widgets\Menu::widget([
                            'items' => [ //TODO 按角色分权限， 先全部写死
                                ['label' => '内容管理' , 'action' => '', 'url' => '#' , 'menu_id' => 1 , 'items' => [
                                        ['label' => '视频管理' , 'action' => '', 'url' => '/videos/index' , 'menu_id' => 4],
                                        ['label' => '习题管理' , 'action' => '', 'url' => '/exercises/index' , 'menu_id' => 5],
                                        ['label' => 'Review管理' , 'action' => '', 'url' => '/reviews/index' , 'menu_id' => 9],
                                        ['label' => '投诉建议' , 'action' => '', 'url' => '/tickets/index' , 'menu_id' => 6],
                                ],],
                                ['label' => '客户管理' , 'action' => '', 'url' => '#' , 'menu_id' => 2 , 'items' => [
                                    ['label' => '用户列表' , 'action' => '', 'url' => '/users/index' , 'menu_id' => 7],
                                ],],
                                ['label' => '员工管理' , 'action' => '', 'url' => '#' , 'menu_id' => 3 , 'items' => [
                                    ['label' => '员工列表' , 'action' => '', 'url' => '/admins/index' , 'menu_id' => 8],
                                ],],
                            ],
                        ]); ?>

                    </div>
                </div>
                <!-- /sidebar menu -->

                <!-- /menu footer buttons -->
                <div class="sidebar-footer hidden-small">
                    <a data-toggle="tooltip" data-placement="top" title="Settings">
                        <span class="glyphicon glyphicon-cog" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="FullScreen">
                        <span class="glyphicon glyphicon-fullscreen" aria-hidden="true"></span>
                    </a>
                    <a data-toggle="tooltip" data-placement="top" title="Lock">
                        <span class="glyphicon glyphicon-eye-close" aria-hidden="true"></span>
                    </a>
                    <a href="/site/logout" data-toggle="tooltip" data-placement="top" title="Logout">
                        <span class="glyphicon glyphicon-off" aria-hidden="true"></span>
                    </a>
                </div>
                <!-- /menu footer buttons -->
            </div>
        </div>

        <!-- top navigation -->
        <div class="top_nav">
            <div class="nav_menu">
                <nav class="" role="navigation">
                    <div class="nav toggle">
                        <a id="menu_toggle"><i class="fa fa-bars"></i></a>
                    </div>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="">
                            <a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown"
                               aria-expanded="false">
                                <?= Yii::$app->user->identity->name ?>
                                <span class=" fa fa-angle-down"></span>
                            </a>
                            <ul class="dropdown-menu dropdown-usermenu pull-right">
                                <li><a href="/site/logout"><i class="fa fa-sign-out pull-right"></i> 退出</a>
                                </li>
                            </ul>
                        </li>

                    </ul>
                </nav>
            </div>

        </div>
        <!-- /top navigation -->

        <div class="right_col" role="main">
            <?= Alert::widget(); ?>
            <?= $content ?>
        </div>
        <footer>
            <div class="pull-right">
            </div>
            <div class="clearfix"></div>
        </footer>
    </div>
</div>

<div id="custom_notifications" class="custom-notifications dsp_none">
    <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
    </ul>
    <div class="clearfix"></div>
    <div id="notif-group" class="tabbed_notifications"></div>
</div>
<?php $this->endBody(); ?>
<script src="/js/crypto-js.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(function () {
        if (typeof layer == "undefined" && typeof layui != "undefined") {
            layui.use('layer')
        }
    });
</script>
</body>
</html>
<?php $this->endPage(); ?>
