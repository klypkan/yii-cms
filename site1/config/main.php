<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/params.php'
);

return [
    'id' => 'app-site1',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'site1\controllers',
    'defaultRoute' => 'site/index',
    'language' => $params['defaultLanguage'],
    //'timeZone' => 'Europe/Moscow',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '#cookieValidationKey#',
            'csrfParam' => '_csrf-site1',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-site1', 'httpOnly' => true],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'session' => [
            // this is the name of the session cookie used for login
            'name' => 'advanced-site1',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
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
            //rules for a site with some languages
            'rules' => [
/*                [
                     'pattern' => '<lang>/<controller:(site)>/<action:(login|loguot|signup|request-password-reset|reset-password)>',
                    'route' => '<controller>/<action>',
                    'defaults' => ['lang' => $params['defaultLanguage']],
                ],*/
                [
                    'pattern' => '<lang>/category/<slug>/<page:\d+>/<perPage:\d+>',
                    'route' => $params['categoryRoute'],
                    'defaults' => ['page' => 1, 'perPage' => 20],
                ],
                [
                    'pattern' => '<lang>/tag/<slug>/<page:\d+>/<perPage:\d+>',
                    'route' => $params['tagRoute'],
                    'defaults' => ['page' => 1, 'perPage' => 20],
                ],
                [
                    'pattern' => '<lang>/page/<page:\d+>/<perPage:\d+>',
                    'route' => 'site/index',
                    'defaults' => ['perPage' => 20],
                ],
                [
                    'pattern' => '<lang>/<slug>',
                    'route' => $params['postRoute'],
                ],
                [
                    'pattern' => '<lang>',
                    'route' => 'site/index',
                    'defaults' => ['lang' => $params['defaultLanguage'], 'page' => 1, 'perPage' => 20],
                ],
            ],
            //rules for a site with some languages
            //rules for a site with one language
/*            'rules' => [
//                [
//                     'pattern' => '<controller:(site)>/<action:(login|loguot|signup|request-password-reset|reset-password)>',
//                    'route' => '<controller>/<action>',
//                    'defaults' => ['lang' => $params['defaultLanguage']],
//                ],
                [
                    'pattern' => 'category/<slug>/<page:\d+>/<perPage:\d+>',
                    'route' => $params['categoryRoute'],
                    'defaults' => ['lang' => $params['defaultLanguage'], 'page' => 1, 'perPage' => 20],
                ],
                [
                    'pattern' => 'tag/<slug>/<page:\d+>/<perPage:\d+>',
                    'route' => $params['tagRoute'],
                    'defaults' => ['lang' => $params['defaultLanguage'], 'page' => 1, 'perPage' => 20],
                ],
                [
                    'pattern' => 'page/<page:\d+>/<perPage:\d+>',
                    'route' => 'site/index',
                    'defaults' => ['lang' => $params['defaultLanguage'], 'perPage' => 20],
                ],
                [
                    'pattern' => '<slug>',
                    'route' => $params['postRoute'],
                    'defaults' => ['lang' => $params['defaultLanguage']],
                ],
                [
                    'pattern' => '/',
                    'route' => 'site/index',
                    'defaults' => ['lang' => $params['defaultLanguage'], 'page' => 1, 'perPage' => 20],
                ],
            ],*/
            //rules for a site with one language
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                        'error' => 'error.php',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];
