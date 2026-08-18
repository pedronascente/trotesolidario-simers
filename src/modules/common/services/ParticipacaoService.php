<?php

namespace app\modules\common\services;

use app\models\User;
use app\modules\common\models\Certificado;
use app\modules\common\models\Participacao;
use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use app\modules\common\models\ParticipantUniversityCorrectionForm;
use app\modules\common\models\ParticipantUniversityCorrectionRequestForm;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ParticipacaoService implements ParticipacaoServiceInterface
{
    private RankingCacheServiceInterface $rankingCacheService;
    private CertificadoServiceInterface $certificadoService;

    public function __construct(RankingCacheServiceInterface $rankingCacheService, CertificadoServiceInterface $certificadoService)
    {
        $this->rankingCacheService = $rankingCacheService;
        $this->certificadoService = $certificadoService;
    }

    public function create(Participacao $model): bool
    {
        return Yii::$app->db->transaction(function () use ($model) {
            if (!$this->validateParticipacaoUser($model)) {
                return false;
            }

            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao salvar participacao.');
            }

            $this->rebuildRankingForTrotes([$model->trote_id]);

            return true;
        });
    }

    public function update(Participacao $model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel atualizar uma participacao nao persistida.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            $oldTroteId = $model->getOldAttribute('trote_id');

            if (!$this->validateParticipacaoUser($model)) {
                return false;
            }

            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao atualizar participacao.');
            }

            $this->rebuildRankingForTrotes([$oldTroteId, $model->trote_id]);

            return true;
        });
    }

    public function delete(Participacao $model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel excluir uma participacao nao persistida.');
        }

        if ($this->hasDoacoesVinculadas((int) $model->id)) {
            throw new Exception('Nao e possivel excluir esta participacao porque existem doacoes vinculadas a ela.');
        }

        if ($this->hasCertificadosVinculados((int) $model->id)) {
            throw new Exception('Nao e possivel excluir esta participacao porque existe certificado vinculado a ela.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            $troteId = $model->trote_id;

            if ($model->delete() === false) {
                throw new Exception('Erro ao excluir participacao.');
            }

            $this->rebuildRankingForTrotes([$troteId]);

            return true;
        });
    }

    public function findModel(int $id): ?Participacao
    {
        return Participacao::find()->with(['user', 'trote', 'universidade'])->where(['id' => $id])->one();
    }

    public function findUsers(): array
    {
        $users = $this->findAvailableUsersQuery()->orderBy(['nome' => SORT_ASC])->all();

        return ArrayHelper::map($users, 'id', static function (User $user) {
            return $user->nome . ' | ' . ($user->cpfFormatado ?: '-') . ' | ' . $user->email;
        });
    }

    public function findTrotes(): array
    {
        $trotes = Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all();

        return ArrayHelper::map($trotes, 'id', static function (Trote $trote) {
            $titulo = $trote->titulo ?: 'Sem titulo';
            $edicao = $trote->edicao ?: 'Sem edicao';
            return $titulo . ' | ' . $edicao;
        });
    }

    public function findUniversidades(): array
    {
        $universidades = Universidade::find()->orderBy(['nome' => SORT_ASC])->all();

        return ArrayHelper::map($universidades, 'id', static function (Universidade $universidade) {
            $cidade = $universidade->cidade ?: '-';
            $uf = $universidade->uf ?: '-';
            return $universidade->nome . ' | ' . $cidade . '/' . $uf;
        });
    }

    public function findActiveParticipationsByUserId(int $userId): array
    {
        return Participacao::find()
            ->with(['universidade', 'trote'])
            ->where([
                'user_id' => $userId,
                'status' => Participacao::STATUS_ATIVO,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->all();
    }

    public function getParticipantUniversitySelfCorrectionData(int $userId, ?int $participacaoId = null): array
    {
        $availableParticipacoes = $this->findActiveParticipationsByUserId($userId);
        $participacao = $this->resolveSelectedParticipation($availableParticipacoes, $participacaoId);
        $selectionError = $this->buildSelectionError($participacaoId, $participacao);
        $eligibility = $selectionError !== null
            ? ['allowed' => false, 'reason' => $selectionError]
            : $this->evaluateSelfCorrectionEligibility($participacao);

        $model = new ParticipantUniversityCorrectionForm();
        $model->userId = $userId;

        if ($participacao !== null) {
            $model->participacaoId = (int) $participacao->id;
            $model->currentUniversidadeId = $participacao->universidade_id !== null ? (int) $participacao->universidade_id : null;
            $model->universidade_id = $participacao->universidade_id !== null ? (int) $participacao->universidade_id : null;
        }

        return [
            'model' => $model,
            'participacao' => $participacao,
            'universidades' => $this->findActiveUniversidades(),
            'canSelfCorrect' => $eligibility['allowed'],
            'selfCorrectionReason' => $eligibility['reason'],
            'availableParticipacoes' => $availableParticipacoes,
            'selectedParticipationId' => $participacao ? (int) $participacao->id : null,
        ];
    }

    public function selfCorrectParticipantUniversity(int $userId, int $participacaoId, ParticipantUniversityCorrectionForm $form): Participacao
    {
        $participacao = $this->requireSelectedParticipation($userId, $participacaoId);
        $eligibility = $this->evaluateSelfCorrectionEligibility($participacao);

        if (!$eligibility['allowed']) {
            throw new Exception($eligibility['reason'] ?: 'Nao foi possivel corrigir a universidade agora.');
        }

        $form->participacaoId = (int) $participacao->id;
        $form->currentUniversidadeId = $participacao->universidade_id !== null ? (int) $participacao->universidade_id : null;

        if (!$form->validate()) {
            throw new Exception('Nao foi possivel validar a nova universidade informada.');
        }

        $participacao->universidade_id = (int) $form->universidade_id;

        if (!$participacao->save(false, ['universidade_id', 'updated_at'])) {
            throw new Exception('Nao foi possivel atualizar a universidade da participacao.');
        }

        $this->syncSideEffectsAfterUniversityChange($participacao);

        return $participacao;
    }

    public function getParticipantUniversityCorrectionRequestData(int $userId, ?int $participacaoId = null): array
    {
        $availableParticipacoes = $this->findActiveParticipationsByUserId($userId);
        $participacao = $this->resolveSelectedParticipation($availableParticipacoes, $participacaoId);
        $selectionError = $this->buildSelectionError($participacaoId, $participacao);
        $eligibility = $selectionError !== null
            ? ['allowed' => false, 'reason' => $selectionError]
            : $this->evaluateRequestEligibility($participacao);

        $model = new ParticipantUniversityCorrectionRequestForm();
        $model->userId = $userId;
        $latestRequest = $participacao ? $this->findLatestUniversityCorrectionRequestByParticipationId((int) $participacao->id) : null;
        $pendingRequest = $latestRequest !== null && $latestRequest->status === ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE
            ? $latestRequest
            : null;

        if ($participacao !== null) {
            $model->participacaoId = (int) $participacao->id;
            $model->currentUniversidadeId = $participacao->universidade_id !== null ? (int) $participacao->universidade_id : null;
        }

        return [
            'model' => $model,
            'participacao' => $participacao,
            'universidades' => $this->findActiveUniversidades(),
            'canRequestCorrection' => $eligibility['allowed'],
            'requestCorrectionReason' => $eligibility['reason'],
            'pendingRequest' => $pendingRequest,
            'latestRequest' => $latestRequest,
            'availableParticipacoes' => $availableParticipacoes,
            'selectedParticipationId' => $participacao ? (int) $participacao->id : null,
        ];
    }

    public function submitParticipantUniversityCorrectionRequest(int $userId, int $participacaoId, ParticipantUniversityCorrectionRequestForm $form): ParticipacaoUniversidadeChangeRequest
    {
        $participacao = $this->requireSelectedParticipation($userId, $participacaoId);
        $eligibility = $this->evaluateRequestEligibility($participacao);

        if (!$eligibility['allowed']) {
            throw new Exception($eligibility['reason'] ?: 'Nao foi possivel registrar a solicitacao.');
        }

        $form->participacaoId = (int) $participacao->id;
        $form->currentUniversidadeId = $participacao->universidade_id !== null ? (int) $participacao->universidade_id : null;

        if (!$form->validate()) {
            throw new Exception('Nao foi possivel validar a solicitacao de correcao.');
        }

        $request = new ParticipacaoUniversidadeChangeRequest();
        $request->participacao_id = (int) $participacao->id;
        $request->old_universidade_id = (int) $participacao->universidade_id;
        $request->new_universidade_id = (int) $form->new_universidade_id;
        $request->motivo = $form->motivo;
        $request->status = ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE;
        $request->requested_by = $userId;
        $request->created_at = date('Y-m-d H:i:s');

        if (!$request->save()) {
            throw new Exception('Nao foi possivel salvar a solicitacao: ' . json_encode($request->errors));
        }

        return $request;
    }

    public function findUniversityCorrectionRequest(int $id): ?ParticipacaoUniversidadeChangeRequest
    {
        return ParticipacaoUniversidadeChangeRequest::find()
            ->with(['participacao.user', 'participacao.trote', 'participacao.universidade', 'oldUniversidade', 'newUniversidade', 'solicitante', 'revisor'])
            ->where(['id' => $id])
            ->one();
    }

    public function findUniversityCorrectionRequests(?string $status = null, int $limit = 100): array
    {
        return ParticipacaoUniversidadeChangeRequest::find()
            ->with(['participacao.user', 'participacao.trote', 'oldUniversidade', 'newUniversidade', 'solicitante', 'revisor'])
            ->andFilterWhere(['status' => $status])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit)
            ->all();
    }

    public function approveUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest
    {
        Yii::$app->db->transaction(function () use ($requestId, $reviewedBy, $reviewNotes) {
            $request = $this->findPendingUniversityCorrectionRequestForUpdateOrFail($requestId);
            $participacao = $this->findModel((int) $request->participacao_id);

            if ($participacao === null) {
                throw new Exception('Participacao vinculada a solicitacao nao encontrada.');
            }

            $currentUniversidadeId = (int) $participacao->universidade_id;
            $oldUniversidadeId = (int) $request->old_universidade_id;
            $newUniversidadeId = (int) $request->new_universidade_id;

            if (!Universidade::find()->where(['id' => $newUniversidadeId, 'ativo' => 1])->exists()) {
                throw new Exception('A universidade solicitada nao esta mais ativa. Revise a solicitacao antes de aprovar.');
            }

            if ($currentUniversidadeId !== $oldUniversidadeId && $currentUniversidadeId !== $newUniversidadeId) {
                throw new Exception('A participacao foi alterada depois que a solicitacao foi aberta. Revise o estado atual antes de concluir a analise.');
            }

            if ($currentUniversidadeId !== $newUniversidadeId) {
                $participacao->universidade_id = $newUniversidadeId;
                if (!$participacao->save(false, ['universidade_id', 'updated_at'])) {
                    throw new Exception('Nao foi possivel atualizar a universidade da participacao.');
                }
            }

            $request->status = ParticipacaoUniversidadeChangeRequest::STATUS_APROVADO;
            $request->reviewed_by = $reviewedBy;
            $request->review_notes = $reviewNotes;
            $request->reviewed_at = date('Y-m-d H:i:s');

            if (!$request->save(false, ['status', 'reviewed_by', 'review_notes', 'reviewed_at'])) {
                throw new Exception('Nao foi possivel atualizar a solicitacao.');
            }

            $this->syncSideEffectsAfterUniversityChange($participacao);
        });

        return $this->findUniversityCorrectionRequest($requestId);
    }

    public function rejectUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest
    {
        Yii::$app->db->transaction(function () use ($requestId, $reviewedBy, $reviewNotes) {
            $request = $this->findPendingUniversityCorrectionRequestForUpdateOrFail($requestId);
            $request->status = ParticipacaoUniversidadeChangeRequest::STATUS_REJEITADO;
            $request->reviewed_by = $reviewedBy;
            $request->review_notes = $reviewNotes;
            $request->reviewed_at = date('Y-m-d H:i:s');

            if (!$request->save(false, ['status', 'reviewed_by', 'review_notes', 'reviewed_at'])) {
                throw new Exception('Nao foi possivel rejeitar a solicitacao.');
            }
        });

        return $this->findUniversityCorrectionRequest($requestId);
    }

    protected function hasDoacoesVinculadas(int $participacaoId): bool
    {
        return (new Query())->from('doacao')->where(['participacao_id' => $participacaoId])->exists();
    }

    protected function hasCertificadosVinculados(int $participacaoId): bool
    {
        return (new Query())->from('certificado')->where(['participacao_id' => $participacaoId])->exists();
    }

    protected function validateParticipacaoUser(Participacao $model): bool
    {
        $userId = (int) $model->user_id;
        if ($userId <= 0 || !$this->isUserAvailableForParticipacao($userId)) {
            $model->addError('user_id', 'Selecione um usuario participante ativo.');
            return false;
        }

        return true;
    }

    protected function isUserAvailableForParticipacao(int $userId): bool
    {
        return $this->findAvailableUsersQuery()->andWhere(['id' => $userId])->exists();
    }

    protected function findAvailableUsersQuery()
    {
        return User::find()->where([
            'role' => User::ROLE_PARTICIPANTE,
            'status' => User::STATUS_ACTIVE,
        ]);
    }

    private function evaluateSelfCorrectionEligibility(?Participacao $participacao): array
    {
        if ($participacao === null) {
            return ['allowed' => false, 'reason' => 'Selecione uma participacao ativa para corrigir a universidade.'];
        }

        if ($participacao->status !== Participacao::STATUS_ATIVO) {
            return ['allowed' => false, 'reason' => 'A autocorrecao so esta disponivel para participacoes ativas.'];
        }

        return ['allowed' => false, 'reason' => 'Toda alteracao de universidade deve ser enviada para analise da administracao.'];
    }

    private function evaluateRequestEligibility(?Participacao $participacao): array
    {
        if ($participacao === null) {
            return ['allowed' => false, 'reason' => 'Selecione uma participacao ativa para solicitar a correcao de universidade.'];
        }

        if ($this->evaluateSelfCorrectionEligibility($participacao)['allowed']) {
            return ['allowed' => false, 'reason' => 'Esta participacao ainda pode ser corrigida diretamente sem abrir solicitacao.'];
        }

        if ($this->findPendingUniversityCorrectionRequestByParticipationId((int) $participacao->id) !== null) {
            return ['allowed' => false, 'reason' => 'Ja existe uma solicitacao pendente para a participacao selecionada.'];
        }

        return ['allowed' => true, 'reason' => null];
    }

    private function resolveSelectedParticipation(array $availableParticipacoes, ?int $participacaoId): ?Participacao
    {
        if ($participacaoId !== null) {
            foreach ($availableParticipacoes as $participacao) {
                if ((int) $participacao->id === $participacaoId) {
                    return $participacao;
                }
            }

            return null;
        }

        if (count($availableParticipacoes) === 1) {
            return $availableParticipacoes[0];
        }

        return null;
    }

    private function requireSelectedParticipation(int $userId, int $participacaoId): Participacao
    {
        $participacao = $this->resolveSelectedParticipation(
            $this->findActiveParticipationsByUserId($userId),
            $participacaoId
        );

        if ($participacao === null) {
            throw new Exception('Selecione uma participacao ativa valida.');
        }

        return $participacao;
    }

    private function buildSelectionError(?int $participacaoId, ?Participacao $participacao): ?string
    {
        if ($participacaoId !== null && $participacao === null) {
            return 'A participacao selecionada e invalida ou nao esta mais ativa.';
        }

        return null;
    }

    private function findPendingUniversityCorrectionRequestByParticipationId(int $participacaoId): ?ParticipacaoUniversidadeChangeRequest
    {
        return ParticipacaoUniversidadeChangeRequest::find()
            ->with(['oldUniversidade', 'newUniversidade'])
            ->where([
                'participacao_id' => $participacaoId,
                'status' => ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE,
            ])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->one();
    }

    private function findLatestUniversityCorrectionRequestByParticipationId(int $participacaoId): ?ParticipacaoUniversidadeChangeRequest
    {
        return ParticipacaoUniversidadeChangeRequest::find()
            ->with(['oldUniversidade', 'newUniversidade', 'revisor'])
            ->where(['participacao_id' => $participacaoId])
            ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
            ->one();
    }

    private function findPendingUniversityCorrectionRequestForUpdateOrFail(int $requestId): ParticipacaoUniversidadeChangeRequest
    {
        Yii::$app->db->createCommand(
            'SELECT id FROM participacao_universidade_change_request WHERE id = :id FOR UPDATE',
            [':id' => $requestId]
        )->queryScalar();

        return $this->findPendingUniversityCorrectionRequestOrFail($requestId);
    }

    private function findPendingUniversityCorrectionRequestOrFail(int $requestId): ParticipacaoUniversidadeChangeRequest
    {
        $request = $this->findUniversityCorrectionRequest($requestId);
        if ($request === null) {
            throw new Exception('Solicitacao de correcao nao encontrada.');
        }

        if ($request->status !== ParticipacaoUniversidadeChangeRequest::STATUS_PENDENTE) {
            throw new Exception('A solicitacao informada ja foi analisada.');
        }

        return $request;
    }

    private function findActiveUniversidades(): array
    {
        $universidades = Universidade::find()->where(['ativo' => 1])->orderBy(['nome' => SORT_ASC])->all();

        return ArrayHelper::map($universidades, 'id', static function (Universidade $universidade) {
            $cidade = $universidade->cidade ?: '-';
            $uf = $universidade->uf ?: '-';
            return $universidade->nome . ' | ' . $cidade . '/' . $uf;
        });
    }

    private function syncSideEffectsAfterUniversityChange(Participacao $participacao): void
    {
        if ($participacao->trote_id !== null) {
            $this->rankingCacheService->rebuild((int) $participacao->trote_id);
        }

        $certificado = Certificado::findOne(['participacao_id' => $participacao->id]);
        if ($certificado !== null) {
            $this->certificadoService->ensurePdf($certificado, true);
        }
    }

    private function rebuildRankingForTrotes(array $troteIds): void
    {
        $troteIds = array_values(array_unique(array_filter(array_map('intval', $troteIds))));

        foreach ($troteIds as $troteId) {
            $this->rankingCacheService->rebuild($troteId);
        }
    }
}
