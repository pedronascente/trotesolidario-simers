<?php

namespace app\modules\common\services;

use Yii;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;
use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use app\modules\common\models\TipoDoacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\models\Users;
use app\modules\common\services\contracts\DoacaoServiceInterface;
class DoacaoService implements DoacaoServiceInterface
{
    public function create($model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $arquivo = UploadedFile::getInstance($model, 'file');

            if ($arquivo) {
                $path = $this->saveArquivo($arquivo);

                if (!$path) {
                    throw new \Exception('Erro ao salvar arquivo');
                }

                $model->arquivo = $path;
            }

            if (!$model->save()) {
                throw new \Exception('Erro ao salvar doação');
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    public function update($model): bool
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $arquivo = UploadedFile::getInstance($model, 'file');

            if ($arquivo) {

                $caminho = Yii::getAlias('@imgArquivosDoacao');
                $arquivoAntigo = $model->getOldAttribute('arquivo');

                if ($arquivoAntigo) {
                    $oldPath = rtrim($caminho, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $arquivoAntigo;

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $nome = Yii::$app->security->generateRandomString() . '.' . $arquivo->extension;

                $path = $this->saveArquivo($arquivo);

                if (!$path) {
                    throw new \Exception('Erro ao salvar novo arquivo');
                }

                $model->arquivo = $path;
            }

            if (!$model->save()) {
                throw new \Exception('Erro ao atualizar doação');
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            return false;
        }
    }

    private function saveArquivo(UploadedFile $arquivo): ?string
    {
        $nome = uniqid('doacao_') . '.' . $arquivo->extension;

        $caminho = Yii::getAlias('@imgArquivosDoacao');

        if (!is_dir($caminho)) {
            mkdir($caminho, 0777, true);
        }

        $fullPath = rtrim($caminho, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $nome;

        if ($arquivo->saveAs($fullPath)) {
            return $nome; // 🔥 só o nome
        }

        return null;
    }

    public function getFormData(): array
    {
        return [
            'trote' => ArrayHelper::map(
                Trote::find()->where(['status' => 'ativo'])->orderBy('titulo')->all(),
                'id',
                'titulo'
            ),
            'evento' => ArrayHelper::map(
                Evento::find()->orderBy('nome')->all(),
                'id',
                'nome'
            ),
            'usuario' => ArrayHelper::map(
                Users::find()->orderBy('name')->all(),
                'id',
                'name'
            ),
            'universidade' => ArrayHelper::map(
                Universidade::find()->orderBy('nome')->all(),
                'id',
                'nome'
            ),
            'tipoDoacao' => ArrayHelper::map(
                TipoDoacao::find()->orderBy('nome')->all(),
                'id',
                'nome'
            ),
        ];
    }
    public function getEventosByTrote(int $troteId): array
    {
        return Evento::find()
            ->where(['trote_id' => $troteId])
            ->orderBy('nome')
            ->asArray()
            ->all();
    }

    public function getTiposDisponiveis(int $userId, int $troteId): array
    {
        $tiposJaFeitos = Doacao::find()
            ->select('tipo_doacao_id')
            ->where([
                'user_id' => $userId,
                'trote_id' => $troteId
            ]);

        return TipoDoacao::find()
            ->where(['not in', 'id', $tiposJaFeitos])
            ->asArray()
            ->all();
    }

    public function aprovar(int $id): bool
    {
        $model = Doacao::findOne($id);
        if (!$model) return false;

        $model->status = Doacao::STATUS_APROVADO;
        $model->observacao = null;

        return $model->save(false);
    }

    public function reprovar(int $id, string $observacao): bool
    {
        $model = Doacao::findOne($id);
        if (!$model) return false;

        $model->status = Doacao::STATUS_REJEITADO;
        $model->observacao = $observacao;

        return $model->save(false);
    }
}
