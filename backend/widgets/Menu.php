<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */
namespace backend\widgets;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
class Menu extends \yii\widgets\Menu
{
    /**
     * @inheritdoc
     */
    public $labelTemplate = '{label}';
    /**
     * @inheritdoc
     */
    public $linkTemplate = '<a href="{url}" id="menu_{menu_id}">{icon}<span>{label}</span>{badge}</a>';
    /**
     * @inheritdoc
     */
    public $submenuTemplate = "\n<ul class=\"nav child_menu\">\n{items}\n</ul>\n";
    /**
     * @inheritdoc
     */
    public $activateParents = true;
    /**
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'nav side-menu');
        parent::init();
    }
    /**
     * @inheritdoc
     */
    protected function renderItem($item)
    {
        $renderedItem = parent::renderItem($item);
        if (isset($item['badge'])) {
            $badgeOptions = ArrayHelper::getValue($item, 'badgeOptions', []);
            Html::addCssClass($badgeOptions, 'label pull-right');
        } else {
            $badgeOptions = null;
        }
        return strtr(
            $renderedItem,
            [
                '{menu_id}' => isset($item['menu_id'])?$item['menu_id']:'',
                '{icon}' => isset($item['icon'])?'<i class="fa fa-'.$item['icon'].'"></i>':'',
                '{badge}' => (
                    isset($item['badge'])
                        ? Html::tag('small', $item['badge'], $badgeOptions)
                        : ''
                    ) . (
                        ''
                    ),
            ]
        );
    }

    protected function isItemActive($item)
    {
        if (isset($item['menu_id']) and $item['menu_id'] == Yii::$app->request->get('menu_id',1)) {
            return true;
        }
        return false;
    }
}