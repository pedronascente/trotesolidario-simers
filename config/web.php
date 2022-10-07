<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'name' => 'Trote Solidario',
    'basePath' => dirname(__DIR__),
    'language' => 'pt-br',
    'timeZone' => 'America/Sao_Paulo',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'bundles' => [
                'kartik\form\ActiveFormAsset' => [
                    'bsDependencyEnabled' => false // do not load bootstrap assets for a specific asset bundle
                ],
            ],
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'mDjJbJm3XXWDYy1ln2QyxP75uuVkPCjd',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            /**
             * Chama o model que ira implementar a interface IdentityInterface 
             * e os métodos de autenticação.
             */
            'identityClass' => 'app\modules\participante\models\Users',
            /**
             * É responsável por definir a rota (URL) padrão de login
             */
            'loginUrl' => ['participante/default/index'],
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'smtp.simers@simers.org.br',
                'password' => 'Simers2017',
                'port' => '587',
                'encryption' => 'tls',
            ],
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
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'formatter' => [
            'dateFormat' => 'php:d/m/Y', //'short',
            'datetimeFormat' => 'php:d/m/Y H:i', //'short', dd/n/yy H:m
            'decimalSeparator' => ',',
            'thousandSeparator' => '.',
            'defaultTimeZone' => 'America/Sao_Paulo',
            'currencyCode' => 'R$',
        ],
    ],
    'modules' => [
        'gridview' => [
            'class' => '\kartik\grid\Module',
            'downloadAction' => 'gridview/export/download'
        ],
        'participante' => [
            'class' => 'app\modules\participante\participante',
            'layout' => '@app/modules/participante/views/layouts/participante',
        ],
        'administrator' => [
            'class' => 'app\modules\administrator\administrator',
            'layout' => '@app/modules/participante/views/layouts/participante',
        ],
        'markdown' => [
            // the module class
            'class' => 'kartik\markdown\Module',
            // the controller action route used for markdown editor preview
            'previewAction' => '/markdown/parse/preview',
            // the controller action route used for downloading the markdown exported file
            'downloadAction' => '/markdown/parse/download',
            // the list of custom conversion patterns for post processing
            'customConversion' => [
                '<table>' => '<table class="table table-bordered table-striped">'
            ],
            // whether to use PHP SmartyPantsTypographer to process Markdown output
            'smartyPants' => true,
        ]
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '172.17.0.1'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['127.0.0.1', '::1', '172.17.0.1'],
    ];
}

return $config;
