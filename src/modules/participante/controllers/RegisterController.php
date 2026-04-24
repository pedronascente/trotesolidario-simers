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
            return $this->redirectToParticipantHome();
        }

        $model = $this->createRegistrationForm();
        $cpf = preg_replace('/\D/', '', (string) Yii::$app->request->get('cpf', ''));

        if ($cpf !== '' && strlen($cpf) <= 11 && Yii::$app->request->isGet) {
            $model->cpf = $this->formatCpfForDisplay($cpf);
        }

        if ($model->load(Yii::$app->request->post())) {
            $user = $model->register();
            if ($user !== null && $this->loginUser($user)) {
                Yii::$app->session->setFlash('success', 'Cadastro realizado com sucesso.');
                return $this->redirectToParticipantHome();
            }
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    private function formatCpfForDisplay(string $cpf): string
    {
        if (strlen($cpf) !== 11) {
            return $cpf;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf) ?: $cpf;
    }

    protected function createRegistrationForm(): ParticipantRegistrationForm
    {
        return new ParticipantRegistrationForm();
    }

    protected function loginUser($user): bool
    {
        return Yii::$app->user->login($user);
    }

    protected function redirectToParticipantHome()
    {
        return $this->redirect(['/participante/default/home']);
    }
}
