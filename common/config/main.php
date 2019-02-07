<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => '#dsn#',
            'username' => '#username#',
            'password' => '#password#',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => '',
                'username' => '',
                'password' => '',
                'port' => '587',
                'encryption' => 'tls',
            ],
            'viewPath' => '@common/mail',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'container' => [
        'definitions' => [
        ],
        'singletons' => [
            'common\helpers\SiteHelperInterface' => 'common\helpers\SiteHelper',
            'common\helpers\MenuHelperInterface' => 'common\helpers\MenuHelper'
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => 'common\modules\admin\Module'
        ],
    ],
];
