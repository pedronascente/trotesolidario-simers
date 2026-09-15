<?php

namespace app\modules\common\services;

use app\modules\common\models\CategoriaCusto;
use app\modules\common\models\DistribuicaoCusto;
use app\modules\common\models\Trote;
use app\modules\common\models\TipoCategoriaCusto;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\GestaoCustosServiceInterface;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class GestaoCustosService implements GestaoCustosServiceInterface
{
    public function save(CategoriaCusto $categoria, array $distribuicoes): bool
    {
        $troteIdOriginal = $categoria->isNewRecord ? null : (int) $categoria->getOldAttribute('trote_id');
        if ($troteIdOriginal !== null && (int) $categoria->trote_id !== $troteIdOriginal) {
            $categoria->addError('trote_id', 'Nao e permitido transferir um custo para outro trote.');
            return false;
        }

        $trote = Trote::findOne($troteIdOriginal ?: $categoria->trote_id);
        if ($trote && $trote->status === Trote::STATUS_ENCERRADO) {
            $categoria->addError('trote_id', 'Nao e possivel alterar custos de um trote encerrado.');
            return false;
        }

        $existentes = $categoria->isNewRecord
            ? []
            : DistribuicaoCusto::find()
                ->where(['categoria_custo_id' => $categoria->id])
                ->indexBy('id')
                ->all();
        $models = [];
        $universidades = [];
        $total = 0.0;
        foreach ($distribuicoes as $dados) {
            if (empty($dados['universidade_id']) && empty($dados['valor_previsto']) && empty($dados['observacao'])) {
                continue;
            }
            $id = isset($dados['id']) && $dados['id'] !== '' ? (int) $dados['id'] : null;
            if ($id !== null && !isset($existentes[$id])) {
                $categoria->addError('valor_previsto', 'Uma distribuicao informada nao pertence a esta categoria.');
                return false;
            }

            $model = $id !== null ? $existentes[$id] : new DistribuicaoCusto();
            $model->scenario = DistribuicaoCusto::SCENARIO_BATCH;
            $model->categoria_custo_id = $categoria->id ?: 0;
            $model->universidade_id = $dados['universidade_id'] ?? null;
            $valorDistribuicao = $this->normalizeMoney($dados['valor_previsto'] ?? null);
            if ($valorDistribuicao === null) {
                $categoria->addError('valor_previsto', 'Distribuicao: informe um valor monetario valido.');
                return false;
            }
            $model->valor_previsto = $valorDistribuicao;
            $model->observacao = $dados['observacao'] ?? null;

            if (isset($universidades[(string) $model->universidade_id])) {
                $categoria->addError('valor_previsto', 'Cada universidade pode aparecer apenas uma vez na distribuicao.');
                return false;
            }
            $universidades[(string) $model->universidade_id] = true;
            $total += (float) $model->valor_previsto;
            $models[] = $model;
        }

        $valorCategoria = $this->normalizeMoney($categoria->valor_previsto);
        if ($valorCategoria === null) {
            $categoria->addError('valor_previsto', 'Informe um valor monetario valido.');
            return false;
        }
        $categoria->valor_previsto = $valorCategoria;
        if (!$categoria->validate()) {
            return false;
        }
        if ($total > (float) $categoria->valor_previsto + 0.00001) {
            $categoria->addError('valor_previsto', 'O total distribuido nao pode ultrapassar o valor previsto.');
            return false;
        }

        foreach ($models as $model) {
            $model->categoria_custo_id = $categoria->id ?: 1;
            if (!$model->validate(['universidade_id', 'valor_previsto', 'observacao'])) {
                foreach ($model->getErrors() as $errors) {
                    foreach ($errors as $error) {
                        $categoria->addError('valor_previsto', 'Distribuicao: ' . $error);
                    }
                }
                return false;
            }
        }

        return Yii::$app->db->transaction(function () use ($categoria, $models) {
            if (!$categoria->save(false)) {
                throw new Exception('Erro ao salvar categoria de custo.');
            }

            $idsComUniversidadeAlterada = [];
            foreach ($models as $model) {
                if (!$model->isNewRecord && $model->isAttributeChanged('universidade_id')) {
                    $idsComUniversidadeAlterada[] = (int) $model->id;
                }
            }
            if ($idsComUniversidadeAlterada) {
                DistribuicaoCusto::deleteAll(['id' => $idsComUniversidadeAlterada, 'categoria_custo_id' => $categoria->id]);
                foreach ($models as $model) {
                    if (in_array((int) $model->id, $idsComUniversidadeAlterada, true)) {
                        $model->id = null;
                        $model->setIsNewRecord(true);
                    }
                }
            }

            $idsMantidos = [];
            foreach ($models as $model) {
                if (!$model->isNewRecord) {
                    $idsMantidos[] = (int) $model->id;
                }
            }
            $condicaoExclusao = ['categoria_custo_id' => $categoria->id];
            if ($idsMantidos) {
                $condicaoExclusao = ['and', $condicaoExclusao, ['not in', 'id', $idsMantidos]];
            }
            DistribuicaoCusto::deleteAll($condicaoExclusao);

            foreach ($models as $model) {
                $model->categoria_custo_id = $categoria->id;
                if (!$model->save(false)) {
                    throw new Exception('Erro ao salvar distribuicao de custo.');
                }
            }
            return true;
        });
    }

    public function delete(CategoriaCusto $categoria): bool
    {
        $trote = $categoria->trote;
        if ($trote && $trote->status === Trote::STATUS_ENCERRADO) {
            throw new Exception('Nao e possivel excluir custos de um trote encerrado.');
        }
        return Yii::$app->db->transaction(function () use ($categoria) {
            if ($categoria->delete() === false) {
                throw new Exception('Erro ao excluir categoria de custo.');
            }
            return true;
        });
    }

    public function findModel(int $id): ?CategoriaCusto
    {
        return CategoriaCusto::find()->with(['distribuicoes.universidade', 'trote', 'tipoCategoriaCusto'])->where(['id' => $id])->one();
    }

    public function getTrotes(): array
    {
        return ArrayHelper::map(
            Trote::find()->orderBy(['edicao' => SORT_DESC])->all(),
            'id',
            static fn(Trote $trote) => ($trote->titulo ?: 'Trote Solidario') . ' | ' . $trote->edicao
        );
    }

    public function getUniversidades(array $incluirIds = []): array
    {
        $query = Universidade::find();
        if ($incluirIds) {
            $query->where(['or', ['ativo' => 1], ['id' => array_unique(array_map('intval', $incluirIds))]]);
        } else {
            $query->where(['ativo' => 1]);
        }

        return ArrayHelper::map(
            $query->orderBy(['cidade' => SORT_ASC, 'nome' => SORT_ASC])->all(),
            'id',
            static fn(Universidade $universidade) => $universidade->nome . ' — ' . $universidade->cidade . '/' . $universidade->uf
        );
    }

    public function getTiposCategoria(array $incluirIds = []): array
    {
        $query = TipoCategoriaCusto::find();
        $query->where($incluirIds
            ? ['or', ['ativo' => 1], ['id' => array_unique(array_map('intval', $incluirIds))]]
            : ['ativo' => 1]);

        return ArrayHelper::map($query->orderBy(['nome' => SORT_ASC])->all(), 'id', 'nome');
    }

    public function getDashboard(?int $troteId): array
    {
        $query = CategoriaCusto::find()
            ->with(['distribuicoes.universidade', 'trote', 'tipoCategoriaCusto'])
            ->joinWith('tipoCategoriaCusto')
            ->orderBy([TipoCategoriaCusto::tableName() . '.nome' => SORT_ASC]);
        if ($troteId) {
            $query->andWhere(['trote_id' => $troteId]);
        }
        $categorias = $query->all();
        $porUniversidade = [];
        $totalPrevisto = 0.0;
        foreach ($categorias as $categoria) {
            $totalPrevisto += (float) $categoria->valor_previsto;
            foreach ($categoria->distribuicoes as $distribuicao) {
                $id = (int) $distribuicao->universidade_id;
                if (!isset($porUniversidade[$id])) {
                    $porUniversidade[$id] = ['universidade' => $distribuicao->universidade, 'valor_previsto' => 0.0];
                }
                $porUniversidade[$id]['valor_previsto'] += (float) $distribuicao->valor_previsto;
            }
        }
        uasort($porUniversidade, static fn($a, $b) => $b['valor_previsto'] <=> $a['valor_previsto']);

        return compact('categorias', 'porUniversidade', 'totalPrevisto');
    }

    private function normalizeMoney($value): ?string
    {
        $value = trim((string) $value);

        $formatoBrasileiro = preg_match('/^(?:\d+|\d{1,3}(?:\.\d{3})+)(?:,\d{1,2})?$/', $value);
        $formatoDecimal = preg_match('/^\d+(?:\.\d{1,2})?$/', $value);
        if (!$formatoBrasileiro && !$formatoDecimal) {
            return null;
        }

        if (strpos($value, ',') !== false || preg_match('/^\d{1,3}(?:\.\d{3})+(?:,\d{1,2})?$/', $value)) {
            $value = str_replace('.', '', $value);
            $value = str_replace(',', '.', $value);
        }
        return number_format((float) $value, 2, '.', '');
    }
}