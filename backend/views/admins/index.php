<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 */

$this->title = '员工管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apps-index">
    <p>
        <?= Html::a('创建员工账号', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'name',
            'mobile',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'label' => '状态',
                'value'=>function($model, $key, $index, $column){
                    return $model->status == 1 ? '正常':'封禁';
                }
            ],
            [
                'class' => 'backend\widgets\grid\ActionColumn',
                'template' => ' {update} {ban} {reset-ga}',
                'buttons' => [
                    'ban' => function ($url, $model, $key) {
                        $options = [
                            'class' => 'btn btn-xs btn-danger',
                            'data-href' => $url,
                            'data-pjax' => '0',
                            'onclick' => 'var button = $(this)
                                 $.ajax(button.attr("data-href"), {
                                     type: "POST"
                                 }).done(function(data) {
                                      window.location.reload()
                                 });',
                        ];
                        return Html::a('封停', 'javascript:void(0)', $options);
                    },
                    'reset-ga' => function ($url, $model, $key) {
                        $options = [
                            'class' => 'btn btn-xs btn-danger',
                            'data-href' => $url,
                            'data-pjax' => '0',
                            'onclick' => 'var button = $(this)
                                 $.ajax(button.attr("data-href"), {
                                     type: "POST"
                                 }).done(function(data) {
                                      window.location.reload()
                                 });',
                        ];
                        return Html::a('重置谷歌验证器', 'javascript:void(0)', $options);
                    },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
