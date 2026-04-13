<?php

namespace app\modules\participante\controllers;

use app\modules\common\models\ParticipantRequestPasswordResetForm;
use Yii;
use yii\web\Controller;

class RecoveryPasswordController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'register';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        $model = new ParticipantRequestPasswordResetForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'Verifique seu e-mail para redefinir a senha.');
            return $this->redirect(['/auth/login']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionNewPassowrd($token)
    {
        return $this->redirect(['/participante/new-password/index', 'token' => $token]);
    }
}
