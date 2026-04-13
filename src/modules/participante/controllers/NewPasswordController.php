<?php

namespace app\modules\participante\controllers;

use app\models\ResetPasswordForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class NewPasswordController extends Controller
{
    public function actionIndex($token)
    {
        $this->layout = 'register';

        if (!Yii::$app->user->isGuest) {
            return $this->redirect(['default/home']);
        }

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['/auth/login']);
        } catch (\Throwable $e) {
            Yii::error('Falha ao abrir redefinicao de senha do participante: ' . $e->getMessage(), __METHOD__);
            throw new BadRequestHttpException('Nao foi possivel validar o link de redefinicao agora.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Senha atualizada com sucesso.');
            return $this->redirect(['/auth/login']);
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }
}
