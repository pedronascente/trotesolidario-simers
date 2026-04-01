<?php

namespace app\modules\common\services;

use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use app\modules\common\models\Participacao;
use app\modules\common\models\TipoDoacao;
use app\modules\common\services\contracts\CertificadoServiceInterface;
use Yii;
use yii\db\IntegrityException;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class DoacaoService implements \app\modules\common\services\contracts\DoacaoServiceInterface
{
    private CertificadoServiceInterface $certificadoService;

    public function __construct(CertificadoServiceInterface $certificadoService)
    {
        $this->certificadoService = $certificadoService;
    }

    public function create($model): bool
    {
        return $this->saveModel($model, true);
    }

    public function update($model): bool
    {
        return $this->saveModel($model, false);
    }

    public function getFormData(): array
    {
        $participacoes = Participacao::find()
            ->with(['user', 'trote', 'universidade'])
            ->where(['status' => Participacao::STATUS_ATIVO])
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

        if (!$participacao) {
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

            $this->certificadoService->syncFromApprovedDoacao($model, (int) Yii::$app->user->id);

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

        $model->status = Doacao::STATUS_REJEITADA;
        $model->motivo_reprovado = $motivoReprovado;
        $model->validado_por = Yii::$app->user->id;
        $model->validado_em = date('Y-m-d H:i:s');

        return $model->save(false);
    }

    private function saveModel(Doacao $model, bool $isNew): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $participacao = Participacao::findOne($model->participacao_id);
            if (!$participacao) {
                $model->addError('participacao_id', 'Participacao nao encontrada.');
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
                $arquivoAntigo = !$isNew ? $model->getOldAttribute('arquivo') : null;
                $path = $this->saveArquivo($arquivo);

                if (!$path) {
                    $model->addError('file', 'Erro ao salvar arquivo.');
                    $transaction->rollBack();
                    return false;
                }

                $model->arquivo = $path;

                if ($arquivoAntigo) {
                    $this->removeArquivo($arquivoAntigo);
                }
            }

            if (!$model->save(false)) {
                $model->addError('arquivo', 'Erro ao salvar doacao.');
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();
            return true;
        } catch (IntegrityException $e) {
            $transaction->rollBack();
            $this->mapIntegrityError($model, $e);
            Yii::error($e->getMessage(), __METHOD__);
            return false;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $model->addError('arquivo', 'Erro interno ao salvar doacao.');
            Yii::error($e->getMessage(), __METHOD__);
            return false;
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
        $nome = uniqid('doacao_', true) . '.' . $arquivo->extension;
        $caminho = Yii::getAlias('@imgArquivosDoacao');

        if (!is_dir($caminho)) {
            mkdir($caminho, 0777, true);
        }

        $fullPath = rtrim($caminho, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nome;

        return $arquivo->saveAs($fullPath) ? $nome : null;
    }

    private function removeArquivo(?string $arquivo): void
    {
        if (!$arquivo) {
            return;
        }

        $fullPath = rtrim(Yii::getAlias('@imgArquivosDoacao'), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $arquivo;

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
