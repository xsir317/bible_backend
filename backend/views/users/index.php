<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = '用户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-index">
    <div class="provider-search">
        <?= Html::beginForm(['index'], 'get', ['data-pjax' => '#data-list']); ?>
        <div class="row">
            <div class="col-md-3">
                <label>负责人</label>
                <?= Html::textInput('in_charge', $_GET['in_charge'] ?? '', ['class' => 'form-control', 'placeholder' => '']) ?>
            </div>
            <div class="col-md-3">
                -
            </div>
            <div class="col-md-3">
                -
            </div>
        </div>
        <br>
        <?= Html::submitButton('查询', ['class' => 'btn btn-primary']) ?>
        <?= Html::endForm(); ?>

    </div>


    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'nickname',
            'coins',
            [
                'attribute' => 'grade',
                'format' => 'raw',
                'label' => '级别',
                'value'=>function($model, $key, $index, $column){
                    return \common\repository\UserRepo::showLevelStr($model->grade);
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'label' => '注册时间',
                'value'=>function($model, $key, $index, $column){
                    return $model->created_at;
                }
            ],
            [
                'class' => 'backend\widgets\grid\ActionColumn',
                'template' => '{view}',
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
