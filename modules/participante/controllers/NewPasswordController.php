<?php

namespace app\modules\participante\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\modules\participante\models\RecoveryForm;
use app\modules\participante\models\Helper;
use app\modules\participante\models\Users;

/**
 * Default controller for the `participante` module
 */
class NewPasswordController extends Controller
{

    public $enableCsrfValidation = false;

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex($token)
    {
        $this->layout = 'register';
        $model = Users::findByPasswordResetToken($token);
        if ($model) {
            $model->passwordHash = '';
        }
        if (!$model) {
            Yii::$app->session->setFlash('error', 'Link inválido');
            return $this->goHome();
        }

        if (Yii::$app->request->post()) {
            $model->load(Yii::$app->request->post());
            if ($model->passwordHash != "") {
                $model->setPassword($model->passwordHash);
            }
            //Salva a data de atualização
            $model->setUpdated();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Senha atualizada com sucesso.');
                return $this->goHome();
            } else {
                return $this->render('index', [
                    'model' => $model,
                    'msg' => 'Houve um erro ao salvar sua nova senha',
                    'error' => true
                ]);
            }
        }

        return $this->render('index', [
            'model' => $model,
            'msg' => '',
            'error' => false
        ]);
    }
}
