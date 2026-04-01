<?php

namespace app\controllers;

use Yii;
use app\models\LoginForm;
use app\models\RequestPasswordResetForm;
use app\models\ResetPasswordForm;
use app\services\auth\AuthServiceInterface;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

class AuthController extends Controller
{
    private $authService;

    public function __construct($id, $module, AuthServiceInterface $authService, $config = [])
    {
        $this->authService = $authService;
        parent::__construct($id, $module, $config);
    }

    public function actionLogin()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirectByProfile();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($this->authService->login($model->cpf, $model->password)) {
                return $this->redirectByProfile();
            }

            $model->addError('password', 'Não foi possível concluir o login agora.');
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirectByProfile();
        }

        $model = new RequestPasswordResetForm();

        if ($model->load(Yii::$app->request->post()) && $model->sendEmail()) {
            Yii::$app->session->setFlash('success', 'Enviamos um link para redefinição de senha ao e-mail informado.');
            return $this->redirect(['login']);
        }

        return $this->render('requestPasswordReset', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        $this->layout = 'main-login';

        if (!Yii::$app->user->isGuest) {
            return $this->redirectByProfile();
        }

        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (\Throwable $e) {
            Yii::error('Falha ao abrir redefinição de senha: ' . $e->getMessage(), __METHOD__);
            throw new BadRequestHttpException('Não foi possível validar o link de redefinição agora.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'Sua senha foi redefinida com sucesso.');
            return $this->redirect(['login']);
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    private function redirectByProfile()
    {
        $user = Yii::$app->user->identity;

        if ($user->isParticipante()) {
            return $this->redirect(['/participante/default/index']);
        }

        return $this->redirect(['/administrator/default/index']);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(['login']);
    }
}