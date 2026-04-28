<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;

class SiteController extends Controller
{

    public $enableCsrfValidation = false;


    public function beforeAction($action)
    {
        if ($action->id === 'error') {
            $this->layout = 'error';
        }

        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/participante']);
        }

        return Yii::$app->user->identity->isAdmin()
            ? $this->redirect(['/administrator/default/index'])
            : $this->redirect(['/participante/default/index']);
    }
}
