<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$mailerHost = getenv('MAILER_HOST') ?: ($params['mailerHost'] ?? 'smtp.gmail.com');
$mailerPort = (int) (getenv('MAILER_PORT') ?: ($params['mailerPort'] ?? 587));
$mailerEncryption = getenv('MAILER_ENCRYPTION') ?: ($params['mailerEncryption'] ?? 'tls');
$mailerUsername = getenv('MAILER_USERNAME') ?: ($params['senderEmail'] ?? null);
$mailerPassword = getenv('MAILER_PASSWORD') ?: ($params['mailerPassword'] ?? null);
$mailerUseFileTransport = getenv('MAILER_USE_FILE_TRANSPORT');

$config = [
    'id' => 'basic',
    'name' => 'Trote Solidario',
    'basePath' => dirname(__DIR__),
    'language' => 'pt-BR',
    'timeZone' => 'America/Sao_Paulo',
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@pdf'   => '@app/web/pdf',
        '@img'   => '@app/web/img',
        '@imgArquivosDoacao' => '@app/web/imagens/doacoes',
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
            'csrfParam' => '_csrf',

            'enableCsrfCookie' => true,

            'csrfCookie' => [
                'httpOnly' => true,
                'path' => '/',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            /**
             * Chama o model que ira implementar a interface IdentityInterface 
             * e os mÃ©todos de autenticaÃ§Ã£o.
             */
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            /**
             * Ã‰ responsÃ¡vel por definir a rota (URL) padrÃ£o de login
             */
            'loginUrl' => ['auth/login'],
            //'loginUrl' => ['participante/default/index'],
          
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => $mailerUseFileTransport === false
                ? YII_ENV_DEV
                : filter_var($mailerUseFileTransport, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? YII_ENV_DEV,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $mailerHost,
                'username' => $mailerUsername,
                'password' => $mailerPassword,
                'port' => $mailerPort,
                'encryption' => $mailerEncryption,
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
            'rules' => [
                'auth/login' => 'auth/login',
                'auth/request-password-reset' => 'auth/request-password-reset',
                'auth/reset-password/<token:[^/]+>' => 'auth/reset-password',
            ],
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
        'gridview' => ['class' => 'kartik\grid\Module'],
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
    'container' => [
       'definitions' => require __DIR__ . '/container.php',
        
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;


