<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\ParticipantRegistrationForm;
use Yii;
use yii\web\Controller;

class RegisterController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'register';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new ParticipantRegistrationForm();

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user !== null && Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', 'Cadastro realizado com sucesso.');
                return $this->redirect(['default/home']);
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
