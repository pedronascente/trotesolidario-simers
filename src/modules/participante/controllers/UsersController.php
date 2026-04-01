<?php

namespace app\modules\participante\controllers;

use app\models\User;
use app\modules\common\models\ParticipantProfileForm;
use app\modules\common\models\Participacao;
use app\modules\common\models\Participante;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UsersController extends Controller
{
    public function behaviors()
    {
        return [];
    }

    public function beforeAction($action)
    {
        if ($action->id !== 'perfil') {
            return parent::beforeAction($action);
        }

        if (Yii::$app->user->isGuest) {
            if (YII_ENV_DEV) {
                return parent::beforeAction($action);
            }

            return $this->redirect(['/auth/login']);
        }

        if (!Yii::$app->user->identity->isParticipante()) {
            throw new ForbiddenHttpException('Acesso negado');
        }

        return parent::beforeAction($action);
    }

    public function actionPerfil()
    {
        $this->layout = 'adminindex';

        $user = $this->findCurrentUser();
        $participante = $this->findOrCreateParticipante($user);
        $form = $this->buildForm($user, $participante);

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $user->nome = $form->nome;
                $user->email = $form->email;
                $user->username = $form->username;
                $user->cpf = $form->cpf;

                if (!$user->save(false, ['nome', 'email', 'username', 'cpf', 'updated_at'])) {
                    throw new \RuntimeException('Erro ao atualizar dados do usuario.');
                }

                if ($form->password !== '') {
                    $user->setPassword($form->password);
                    $user->generateAuthKey();

                    if (!$user->save(false, ['password_hash', 'authKey', 'updated_at'])) {
                        throw new \RuntimeException('Erro ao atualizar senha do usuario.');
                    }
                }

                $participante->user_id = $user->id;
                $participante->estudante = (int) $form->estudante;
                $participante->estudante_medicina = (int) $form->estudante_medicina;
                $participante->previsao_formatura = $form->previsao_formatura ?: null;

                if (!$participante->save()) {
                    throw new \RuntimeException('Erro ao atualizar perfil de participante: ' . json_encode($participante->errors));
                }

                $transaction->commit();
                Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso.');

                return $this->refresh();
            } catch (\Throwable $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('perfil', [
            'model' => $form,
            'user' => $user,
            'participante' => $participante,
            'universidadeAtual' => $this->findCurrentUniversityName($user),
        ]);
    }

    private function findCurrentUser(): User
    {
        if (!Yii::$app->user->isGuest) {
            $user = User::findOne(Yii::$app->user->id);
            if ($user === null) {
                throw new NotFoundHttpException('Usuario nao encontrado.');
            }

            return $user;
        }

        if (!YII_ENV_DEV) {
            throw new ForbiddenHttpException('Acesso negado');
        }

        $user = User::find()
            ->where([
                'role' => User::ROLE_PARTICIPANTE,
                'status' => User::STATUS_ACTIVE,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        if ($user === null) {
            throw new NotFoundHttpException('Nenhum participante ativo foi encontrado para teste.');
        }

        return $user;
    }

    private function findOrCreateParticipante(User $user): Participante
    {
        $participante = Participante::findOne(['user_id' => $user->id]);
        if ($participante !== null) {
            return $participante;
        }

        return new Participante([
            'user_id' => $user->id,
            'estudante' => 1,
            'estudante_medicina' => 0,
        ]);
    }

    private function findCurrentUniversityName(User $user): ?string
    {
        $participacao = Participacao::find()
            ->with('universidade')
            ->where(['user_id' => $user->id])
            ->orderBy([
                'status' => SORT_ASC,
                'id' => SORT_DESC,
            ])
            ->one();

        return $participacao && $participacao->universidade
            ? $participacao->universidade->nome
            : null;
    }

    private function buildForm(User $user, Participante $participante): ParticipantProfileForm
    {
        $form = new ParticipantProfileForm();
        $form->userId = (int) $user->id;
        $form->nome = $user->nome;
        $form->email = $user->email;
        $form->username = $user->username;
        $form->cpf = $user->getCpfFormatado() ?? $user->cpf;
        $form->estudante = (int) $participante->estudante;
        $form->estudante_medicina = (int) $participante->estudante_medicina;
        $form->previsao_formatura = $participante->previsao_formatura;

        return $form;
    }
}
