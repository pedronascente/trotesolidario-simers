<?php

namespace app\modules\administrator\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\participante\models\LoginFormAdministrator;
use app\modules\participante\models\Doacao;
use app\modules\participante\models\Helper;
use app\modules\participante\models\Users;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'home'],
                'rules' => [
                    [
                        'actions' => ['logout', 'home'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //                    'logout' => ['post'],
                ],
            ],
        ];
    }


    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $this->layout = 'adminindex';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new LoginFormAdministrator();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->redirect(['default/home']);
        }
        $this->layout = 'login';
        return $this->render('index', ['model' => $model]);
    }


    public function actionHome()
    {
        $this->layout = 'adminindex';
        $dados = '';
        return $this->render('home', [
            'dados' => $dados
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->redirect(['/participante']);
    }
}
