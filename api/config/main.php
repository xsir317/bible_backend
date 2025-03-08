<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'api\controllers',
    'modules' => [
        'user' => [ //用户模块包括用户的各种操作，注册 登录 关注 等等
            'class' => 'api\modules\user\Module',
        ],
        'common' => [ //系统初始化，对时间等等
            'class' => 'api\modules\common\Module',
        ],
        'contents' => [ //系统初始化，对时间等等
            'class' => 'api\modules\contents\Module',
        ],
        //TODO  按不同领域分module
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-api',
            'enableCookieValidation' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
    ],
    'params' => $params,
];
