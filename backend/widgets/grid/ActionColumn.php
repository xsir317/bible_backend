<?php
namespace backend\widgets\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class ActionColumn extends \yii\grid\ActionColumn
{
    protected function initDefaultButtons()
    {
        $this->header = '操作';
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => 'View',
                    'aria-label' => 'View',
                    'class' => 'btn btn-xs btn-info',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                $url_parts = parse_url($url);
                if (isset($url_parts['query'])) {
                    parse_str($url_parts['query'], $params);
                } else {
                    $params = array();
                }
                $params['menu_id'] = Yii::$app->request->get("menu_id");
                $url_parts['query'] = http_build_query($params);
                $url = ($url_parts['path']??'') . '?' . ($url_parts['query']??'');
                return Html::a('查看', $url, $options);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model, $key) {
                $options = array_merge([
                    'title' => 'Update',
                    'aria-label' => 'Update',
                    'class' => 'btn btn-xs btn-success',
                    'data-pjax' => '0',
                ], $this->buttonOptions);
                $url_parts = parse_url($url);
                if (isset($url_parts['query'])) {
                    parse_str($url_parts['query'], $params);
                } else {
                    $params = array();
                }
                $params['menu_id'] = Yii::$app->request->get("menu_id");
                $url_parts['query'] = http_build_query($params);
                $url = ($url_parts['path']??'') . '?' . ($url_parts['query']??'');
                return Html::a('修改', $url, $options);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model, $key) {
                $options = array_merge([
                    'class' => 'btn btn-xs btn-danger',
                    'data-href' => $url,
                    'data-pjax' => '0',
                    'onclick' => 'var button = $(this)
                             $.ajax(button.attr("data-href"), {
                                 type: "POST"
                             }).always(function(data) {
                                  button.parent().parent().remove();
                                  return false
                             }).error(function(data){
                                return false
                             });',
                ],$this->buttonOptions);
                return Html::a('删除', 'javascript:void(0)', $options);
            };
        }
    }
}