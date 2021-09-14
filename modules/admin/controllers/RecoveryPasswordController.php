<?php

namespace app\modules\admin\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\admin\models\RecoveryForm;
use app\modules\admin\models\Helper;
use app\modules\admin\models\Users;

/**
 * Default controller for the `admin` module
 */
class RecoveryPasswordController extends Controller {

    public $enableCsrfValidation = false;

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'home', 'perfil'],
                'rules' => [
                    [
                        'actions' => ['logout', 'home', 'perfil'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex() {
        $msg = '';
        $this->layout = 'register';
        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new RecoveryForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Verifique seu email para recuperar a senha.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Não foi localizado seu cadastro.');
            }
        }

        return $this->render('index', [
                                'model' => $model,
                                'error' => false,
                                'msg' => $msg
                    ]);
    }
    
    public function actionNewPassowrd($token) {
        
        $model = Users::findByPasswordResetToken($token);
        
        return $this->render('index', [
            'model' => $model,
        ]);
        
    }

}
