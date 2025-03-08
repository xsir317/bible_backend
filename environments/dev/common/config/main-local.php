<?php

return [
    'components' => [
        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=localhost;dbname=bible',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@common/mail',
            'useFileTransport' => true,
        ],
        'redis' => [
            'class' => 'common\components\RedisConnection',
            'database' => 0,
            'host' => '127.0.0.1',
            'port' => 6379,
            'prefix' => '',
            'password' => '1234',
        ],
        'cache' => [
            'class' => 'common\components\RedisCache',
        ],
    ],
];
