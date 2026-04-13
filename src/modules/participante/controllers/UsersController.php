<?php

namespace app\modules\participante\controllers;

use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use app\modules\common\services\contracts\UserServiceInterface;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;

class UsersController extends Controller
{
    private $userService;
    private $participacaoService;

    public function __construct($id, $module, UserServiceInterface $userService, ParticipacaoServiceInterface $participacaoService, $config = [])
    {
        $this->userService = $userService;
        $this->participacaoService = $participacaoService;
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [];
    }

    public function beforeAction($action)
    {
        if (!in_array($action->id, ['perfil', 'corrigir-universidade', 'solicitar-correcao-universidade'], true)) {
            return parent::beforeAction($action);
        }

        if (Yii::$app->user->isGuest) {
            Yii::$app->response->redirect(['/auth/login']);
            return false;
        }

        if (!Yii::$app->user->identity->isParticipante()) {
            throw new ForbiddenHttpException('Acesso negado');
        }

        return parent::beforeAction($action);
    }

    public function actionPerfil()
    {
        $this->layout = 'adminindex';

        $userId = (int) Yii::$app->user->id;
        $selectedParticipationId = $this->getSelectedParticipationId();
        $profileData = $this->userService->getParticipantProfileData($userId);
        $form = $profileData['model'];
        $selfCorrectionData = $this->participacaoService->getParticipantUniversitySelfCorrectionData($userId, $selectedParticipationId);
        $requestData = $this->participacaoService->getParticipantUniversityCorrectionRequestData($userId, $selectedParticipationId);
        $selectedParticipacao = $selfCorrectionData['participacao'] ?? null;
        $availableParticipacoes = $selfCorrectionData['availableParticipacoes'] ?? [];

        if ($selectedParticipacao !== null && $selectedParticipacao->universidade) {
            $profileData['universidadeAtual'] = $selectedParticipacao->universidade->nome;
        }
        elseif (count($availableParticipacoes) !== 1) {
            $profileData['universidadeAtual'] = null;
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->userService->updateParticipantProfile($userId, $form);
                Yii::$app->session->setFlash('success', 'Perfil atualizado com sucesso.');

                return $this->refresh();
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('perfil', array_merge($profileData, [
            'canSelfCorrect' => $selfCorrectionData['canSelfCorrect'],
            'selfCorrectionReason' => $selfCorrectionData['selfCorrectionReason'],
            'canRequestCorrection' => $requestData['canRequestCorrection'],
            'requestCorrectionReason' => $requestData['requestCorrectionReason'],
            'pendingRequest' => $requestData['pendingRequest'],
            'availableParticipacoes' => $selfCorrectionData['availableParticipacoes'],
            'selectedParticipationId' => $selfCorrectionData['selectedParticipationId'],
            'selectedParticipacao' => $selectedParticipacao,
        ]));
    }

    public function actionCorrigirUniversidade()
    {
        $this->layout = 'adminindex';

        $userId = (int) Yii::$app->user->id;
        $selectedParticipationId = $this->getSelectedParticipationId();
        $correctionData = $this->participacaoService->getParticipantUniversitySelfCorrectionData($userId, $selectedParticipationId);
        $form = $correctionData['model'];

        if (!$correctionData['canSelfCorrect']) {
            Yii::$app->session->setFlash('error', $correctionData['selfCorrectionReason']);
            return $this->redirect(['perfil', 'participacao_id' => $correctionData['selectedParticipationId']]);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $participacaoId = (int) ($form->participacaoId ?: $correctionData['selectedParticipationId']);
                $this->participacaoService->selfCorrectParticipantUniversity($userId, $participacaoId, $form);
                Yii::$app->session->setFlash('success', 'Universidade atualizada com sucesso na participacao selecionada.');

                return $this->redirect(['perfil', 'participacao_id' => $participacaoId]);
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('corrigir-universidade', $correctionData);
    }

    public function actionSolicitarCorrecaoUniversidade()
    {
        $this->layout = 'adminindex';

        $userId = (int) Yii::$app->user->id;
        $selectedParticipationId = $this->getSelectedParticipationId();
        $requestData = $this->participacaoService->getParticipantUniversityCorrectionRequestData($userId, $selectedParticipationId);
        $form = $requestData['model'];

        if (!$requestData['canRequestCorrection']) {
            Yii::$app->session->setFlash('error', $requestData['requestCorrectionReason']);
            return $this->redirect(['perfil', 'participacao_id' => $requestData['selectedParticipationId']]);
        }

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $participacaoId = (int) ($form->participacaoId ?: $requestData['selectedParticipationId']);
                $this->participacaoService->submitParticipantUniversityCorrectionRequest($userId, $participacaoId, $form);
                Yii::$app->session->setFlash('success', 'Solicitacao enviada para analise da administracao.');

                return $this->redirect(['perfil', 'participacao_id' => $participacaoId]);
            } catch (\Throwable $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('solicitar-correcao-universidade', $requestData);
    }

    private function getSelectedParticipationId(): ?int
    {
        $queryValue = Yii::$app->request->get('participacao_id');
        if ($queryValue === null || $queryValue === '') {
            return null;
        }

        return (int) $queryValue;
    }
}
