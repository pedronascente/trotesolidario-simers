<?php

namespace app\modules\common\services;

use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use app\modules\common\models\Participacao;
use app\modules\common\models\TipoDoacao;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use app\modules\common\services\contracts\DoacaoServiceInterface;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use Yii;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class DoacaoService implements DoacaoServiceInterface
{
    private CertificadoServiceInterface $certificadoService;
    private RankingCacheServiceInterface $rankingCacheService;

    public function __construct(
        CertificadoServiceInterface $certificadoService,
        RankingCacheServiceInterface $rankingCacheService
    ) {
        $this->certificadoService = $certificadoService;
        $this->rankingCacheService = $rankingCacheService;
    }

    public function create($model): bool
    {
        return $this->saveModel($model, true);
    }

    public function update($model): bool
    {
        $model->status = Doacao::STATUS_PENDENTE;
        $model->motivo_reprovado = null;
        return $this->saveModel($model, false);
    }

    public function delete(Doacao $model): bool
    {
        if ($model->status !== Doacao::STATUS_REJEITADA) {
            $model->addError('status', 'Somente doacoes rejeitadas podem ser excluidas.');
            return false;
        }

        if ($this->hasCertificadoEmitido($model)) {
            $model->addError('status', 'Doacoes com certificado emitido nao podem ser excluidas.');
            return false;
        }

        $arquivo = $model->arquivo;
        $transaction = Yii::$app->db->beginTransaction();

        try {
            if ($model->delete() === false) {
                $model->addError('arquivo', 'Erro ao excluir doacao.');
                $transaction->rollBack();
                return false;
            }

            $this->refreshRankingCacheForParticipacoes([(int) $model->participacao_id]);

            $transaction->commit();
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $model->addError('arquivo', 'Erro interno ao excluir doacao.');
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }

        $this->removeArquivo($arquivo);
        return true;
    }

    public function getFormData(): array
    {
        $participacoes = Participacao::find()
            ->with(['user', 'trote', 'universidade'])
            ->joinWith('trote')
            ->where(['participacao.status' => Participacao::STATUS_ATIVO])
            ->andWhere(['<>', 'trote.status', Trote::STATUS_ENCERRADO])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return [
            'participacoes' => ArrayHelper::map($participacoes, 'id', function (Participacao $participacao) {
                return $participacao->getDisplayLabel();
            }),
            'tipoDoacao' => ArrayHelper::map(
                TipoDoacao::find()->where(['ativo' => 1])->orderBy('nome')->all(),
                'id',
                'nome'
            ),
        ];
    }

    public function getEventosByParticipacao(int $participacaoId): array
    {
        $participacao = Participacao::findOne($participacaoId);

        if (!$participacao || $participacao->status !== Participacao::STATUS_ATIVO) {
            return [];
        }

        if ($participacao->trote === null || $participacao->trote->status === Trote::STATUS_ENCERRADO) {
            return [];
        }

        return Evento::find()
            ->where(['trote_id' => $participacao->trote_id])
            ->orderBy('nome')
            ->asArray()
            ->all();
    }

    public function aprovar(int $id): bool
    {
        $model = Doacao::findOne($id);

        if (!$model) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->status = Doacao::STATUS_APROVADA;
            $model->motivo_reprovado = null;
            $model->validado_por = Yii::$app->user->id;
            $model->validado_em = date('Y-m-d H:i:s');

            if (!$model->save(false)) {
                throw new \RuntimeException('Nao foi possivel aprovar a doacao.');
            }

            $this->certificadoService->syncFromApprovedDoacao($model, (int) (Yii::$app->user->id ?? 1));
            $this->refreshRankingCacheForParticipacoes([(int) $model->participacao_id]);

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    public function reprovar(int $id, string $motivoReprovado): bool
    {
        $model = Doacao::findOne($id);

        if (!$model) {
            return false;
        }

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $model->status = Doacao::STATUS_REJEITADA;
            $model->motivo_reprovado = trim($motivoReprovado);
            $model->validado_por = Yii::$app->user->id;
            $model->validado_em = date('Y-m-d H:i:s');

            if (!$model->validate(['status', 'motivo_reprovado', 'validado_por', 'validado_em'])) {
                $transaction->rollBack();
                return false;
            }

            if (!$model->save(false, ['status', 'motivo_reprovado', 'validado_por', 'validado_em', 'updated_at'])) {
                throw new \RuntimeException('Nao foi possivel reprovar a doacao.');
            }

            $this->refreshRankingCacheForParticipacoes([(int) $model->participacao_id]);

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }
    }

    protected function saveModel(Doacao $model, bool $isNew): bool
    {
        $transaction = Yii::$app->db->beginTransaction();
        $oldArquivo = !$isNew ? $model->getOldAttribute('arquivo') : null;
        $novoArquivo = null;

        try {
            $oldParticipacaoId = !$isNew ? (int) ($model->getOldAttribute('participacao_id') ?? 0) : null;

            if (!$isNew && $oldParticipacaoId > 0 && $oldParticipacaoId !== (int) $model->participacao_id) {
                $model->addError('participacao_id', 'Nao e permitido alterar a participacao de uma doacao ja cadastrada.');
                $transaction->rollBack();
                return false;
            }

            $participacao = Participacao::findOne($model->participacao_id);
            if (!$participacao) {
                $model->addError('participacao_id', 'Participacao nao encontrada.');
                $transaction->rollBack();
                return false;
            }

            if ($isNew && $participacao->status !== Participacao::STATUS_ATIVO) {
                $model->addError('participacao_id', 'A participacao selecionada nao esta ativa para registrar doacoes.');
                $transaction->rollBack();
                return false;
            }

            if ($isNew && ($participacao->trote === null
                || $participacao->trote->status === Trote::STATUS_ENCERRADO)) {
                $model->addError('participacao_id', 'Nao e possivel registrar doacoes para um trote encerrado.');
                $transaction->rollBack();
                return false;
            }

            $this->preencherSnapshots($model, $participacao);

            $arquivo = UploadedFile::getInstance($model, 'file');
            $model->file = $arquivo;
            if (!$model->validate()) {
                $transaction->rollBack();
                return false;
            }

            if ($arquivo) {
                $novoArquivo = $this->saveArquivo($arquivo);

                if (!$novoArquivo) {
                    $model->addError('file', 'Erro ao salvar arquivo.');
                    $transaction->rollBack();
                    return false;
                }

                $model->arquivo = $novoArquivo;
            }

            if (!$model->save(false)) {
                $this->cleanupFailedArquivoSave($model, $isNew, $novoArquivo, $oldArquivo);
                $model->addError('arquivo', 'Erro ao salvar doacao.');
                $transaction->rollBack();
                return false;
            }

            if ($model->status === Doacao::STATUS_APROVADA) {
                $this->certificadoService->syncFromApprovedDoacao($model, (int) (Yii::$app->user->id ?? 1));
            }

            $rankingParticipacaoIds = [(int) $model->participacao_id];
            if ($oldParticipacaoId !== null && $oldParticipacaoId > 0) {
                $rankingParticipacaoIds[] = $oldParticipacaoId;
            }
            $this->refreshRankingCacheForParticipacoes($rankingParticipacaoIds);

            $transaction->commit();
        } catch (IntegrityException $e) {
            $transaction->rollBack();
            $this->cleanupFailedArquivoSave($model, $isNew, $novoArquivo, $oldArquivo);
            $this->mapIntegrityError($model, $e);
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->cleanupFailedArquivoSave($model, $isNew, $novoArquivo, $oldArquivo);
            $model->addError('arquivo', 'Erro interno ao salvar doacao.');
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        }

        if ($novoArquivo !== null && $oldArquivo) {
            $this->removeArquivo($oldArquivo);
        }

        return true;
    }

    private function refreshRankingCacheForParticipacoes(array $participacaoIds): void
    {
        $participacaoIds = array_values(array_unique(array_filter(array_map('intval', $participacaoIds))));
        if (empty($participacaoIds)) {
            return;
        }

        $troteIds = Participacao::find()
            ->select('trote_id')
            ->where(['id' => $participacaoIds])
            ->andWhere(['not', ['trote_id' => null]])
            ->column();

        $troteIds = array_values(array_unique(array_map('intval', $troteIds)));
        foreach ($troteIds as $troteId) {
            $this->rankingCacheService->rebuild($troteId);
        }
    }

    private function preencherSnapshots(Doacao $model, Participacao $participacao): void
    {
        $model->cpf_snapshot = $participacao->user->getCpfFormatado() ?? $participacao->user->cpf;
        $model->edicao_snapshot = $participacao->trote->edicao ?? '';
    }

    private function mapIntegrityError(Doacao $model, IntegrityException $e): void
    {
        $message = $e->getMessage();

        if (strpos($message, 'ux_doacao_cpf_edicao_tipo') !== false) {
            $model->addError('tipo_doacao_id', 'Ja existe uma doacao deste tipo para este CPF na edicao informada.');
            return;
        }

        if (strpos($message, "Field 'arquivo' doesn't have a default value") !== false) {
            $model->addError('file', 'Envie um arquivo para a doacao.');
            return;
        }

        $model->addError('arquivo', 'Nao foi possivel salvar a doacao por restricao do banco de dados.');
    }

    private function saveArquivo(UploadedFile $arquivo): ?string
    {
        return DoacaoArquivoStorage::save($arquivo);
    }

    private function removeArquivo(?string $arquivo): void
    {
        DoacaoArquivoStorage::remove($arquivo);
    }

    private function cleanupFailedArquivoSave(
        Doacao $model,
        bool $isNew,
        ?string $novoArquivo,
        ?string $oldArquivo
    ): void
    {
        if ($novoArquivo !== null) {
            $this->removeArquivo($novoArquivo);
        }

        if (!$isNew) {
            $model->arquivo = $oldArquivo;
        }
    }

    protected function hasCertificadoEmitido(Doacao $model): bool
    {
        return Certificado::find()
            ->where(['participacao_id' => $model->participacao_id])
            ->exists();
    }
}
