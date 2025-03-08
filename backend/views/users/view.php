<?php

use common\models\Activity;
use common\models\coin_log\CoinLogType;
use common\models\GameList;
use common\models\GirlContract;
use common\models\LiveAdminLog;
use common\models\RealPersonAuthLog;
use common\models\User;
use common\models\UserAdminLog;
use common\models\UserAuth;
use common\models\UserBank;
use common\models\UserDeductRecord;
use common\models\UserDevice;
use common\models\VideoComment;
use common\services\live\LiveRankService;
use common\services\task\TaskBlacklistService;
use common\services\user\RiskService;
use common\services\user\UserSettingService;
use yii\helpers\Html;
use backend\widgets\Panel;
use yii\widgets\Pjax;
use common\services\game\GameAwardService;
use common\services\stat\GirlStatService;

/* @var $this yii\web\View */

/* @var $model common\models\Users */

$this->title = '用户详细信息';
$this->params['breadcrumbs'][] = ['label' => '列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = ['label' => '', 'url' => '#'];

$uid = $model->id;
?>
<?php Panel::begin(['header' => '用户:' . "{$model->id} 【{$model->nickname}】 (男)" ]); ?>
<style type="text/css">
    li {
        list-style: none;
        line-height: 24px;
    }

    .TopTable {
        padding: 20px 0;
    }

    .table td:nth-child(odd) {
        font-weight: bold;
    }

    #user-admin-log-box td{
        padding-left:12px;
    }

    #user-admin-log-backend-box td{
        padding-left:12px;
    }
</style>
<?php Pjax::begin(['id' => 'data-list']); ?>
<p>
<div class="row">用户资料
<?php
echo Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => '0']);
echo Html::a('跟进', ['follow', 'id' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => '0']);
echo Html::a('课后Review', ['reviews/create', 'uid' => $model->id], ['class' => 'btn btn-success', 'target' => '_blank', 'data-pjax' => '0']);
?>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-3">
            <b>头像</b><br/>-
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <b>所在地</b><br/>
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <b>年龄</b><br/>
            -
        </div>
        <div class="col-md-3 col-sm-3 col-xs-3">
            <b>联系方式</b><br/>
            -
        </div>
    </div>
    <div class="form-group">
        <?php foreach (array_chunk($attributes , 4 , 1) as $_chunk ):?>
            <div class="row">
                <?php foreach ($_chunk as $v):?>
                    <div class="col-md-3">
                        <?= $v['txt'] ?> <?= $v['value'] ?>
                    </div>
                <?php endforeach;?>
            </div>
        <?php endforeach;?>
    </div>
    <div class="row  TopTable">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="col-md-6 col-sm-6 col-xs-6">
                <ul>
                    <li>
                        ID：<?php
                        $content = $model->id;
                        echo $content;
                        ?></li>
                    <li>昵称：<?= $model->nickname ?></li>
                    <li>
                        真实姓名：-</li>
                    <li>-
                    </li>
                    <li>性别：-</li>
                    <li>邀请码：<br />
                        <?= $model->getInviteCode() ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <table class="table table-hover table-condensed">
                <tbody>
                <tr>
                    <td>最后登录IP</td>
                    <td>-</td>
                    <td>最后请求IP</td>
                    <td>-</td>
                </tr>
                <tr>
                    <td>关注</td>
                    <td>-</td>
                    <td>粉丝</td>
                    <td>-</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
