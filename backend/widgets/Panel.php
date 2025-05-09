<?php
/**
 * @copyright Copyright (c) 2015 Yiister
 * @license https://github.com/yiister/yii2-gentelella/blob/master/LICENSE
 * @link http://gentelella.yiister.ru
 */
namespace backend\widgets;
use yii\base\Widget;
use yii\bootstrap\Nav;
use yii\helpers\Html;
class Panel extends Widget
{
    /**
     * @var array the configuration array for creating a [[Dropdown]] widget
     */
    protected $tools = [];
    /**
     * @var array the HTML attributes for the widget container tag
     */
    public $options = ['class' => 'x_panel'];
    /**
     * @var string the panel header
     */
    public $header;
    /**
     * @var string the icon name
     */
    public $icon;
    /**
     * @var boolean whether the expand button is shown
     */
    public $expandable = false;
    /**
     * @var boolean whether the collapse button shown
     */
    public $collapsable = true;
    /**
     * @var boolean whether the remove button shown
     */
    public $removable = true;
    /**
     * @var array|string, optional, the configuration array for creating a [[Dropdown]] widget,
     *   or a string representing the dropdown menu.
     */
    public $headerMenu = [];

    public $breadcrumbs;

    public $id = 'widget-panel';
    /**
     * Init tool buttons
     */
    protected function initTools()
    {
        if ($this->expandable === true || $this->collapsable === true) {
            $this->tools[] = [
                'encode' => false,
                'label' => '<i class="fa fa-chevron-'.($this->expandable === true ? 'down' : 'up').'"></i>',
                'linkOptions' => ['class' => 'collapse-link'],
                'url' => null,
            ];
        }
        if (empty($this->headerMenu) === false) {
            $this->tools[] = [
                'encode' => false,
                'items' => $this->headerMenu,
                'label' => '<i class="fa fa-wrench"></i>',
            ];
        }
        if ($this->removable === true) {
            $this->tools[] = [
                'encode' => false,
                'label' => '<i class="fa fa-close"></i>',
                'linkOptions' => ['class' => 'close-link'],
                'url' => null,
            ];
        }
    }
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->options['id'] = $this->id;
        echo Html::beginTag('div', $this->options);
        if ($this->header !== null) {
            $this->initTools();
            echo Html::beginTag('div', ['class' => 'x_title']);
            echo Html::tag('h2', $this->header);


            if (empty($this->tools) === false) {
                echo Nav::widget(
                    [
                        'dropDownCaret' => '',
                        'items' => $this->tools,
                        'options' => [
                            'class' => 'nav navbar-right panel_toolbox',
                        ],
                    ]
                );
            }
            echo \yii\widgets\Breadcrumbs::widget([
                'links' => isset($this->breadcrumbs) ? $this->breadcrumbs : [],
                'options'=>[
                'class'=>'nav navbar-right panel_toolbox bread'

                ]
            ]);
            echo Html::tag('div', null, ['class' => 'clearfix']);
            echo Html::endTag('div');
        }
        echo Html::beginTag(
            'div',
            [
                'class' => 'x_content',
                'style' => $this->expandable === true ? 'display: none;' : null
            ]
        );
    }
    /**
     * @inheritdoc
     */
    public function run()
    {
        echo Html::endTag('div');
        echo Html::endTag('div');
        parent::run();
    }
}