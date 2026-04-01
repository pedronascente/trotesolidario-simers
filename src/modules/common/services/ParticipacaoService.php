<?php

namespace app\modules\common\services;

use app\models\User;
use app\modules\common\models\Participacao;
use app\modules\common\models\Trote;
use app\modules\common\models\Universidade;
use app\modules\common\services\contracts\ParticipacaoServiceInterface;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ParticipacaoService implements ParticipacaoServiceInterface
{
    public function create(Participacao $model): bool
    {
        return Yii::$app->db->transaction(function () use ($model) {
            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao salvar participacao.');
            }

            return true;
        });
    }

    public function update(Participacao $model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel atualizar uma participacao nao persistida.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao atualizar participacao.');
            }

            return true;
        });
    }

    public function delete(Participacao $model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel excluir uma participacao nao persistida.');
        }

        if ($this->hasDoacoesVinculadas($model->id)) {
            throw new Exception('Nao e possivel excluir esta participacao porque existem doacoes vinculadas a ela.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if ($model->delete() === false) {
                throw new Exception('Erro ao excluir participacao.');
            }

            return true;
        });
    }

    public function findModel(int $id): ?Participacao
    {
        return Participacao::find()->with(['user', 'trote', 'universidade'])->where(['id' => $id])->one();
    }

    public function findUsers(): array
    {
        $users = User::find()->orderBy(['nome' => SORT_ASC])->all();

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

    private function hasDoacoesVinculadas(int $participacaoId): bool
    {
        return (new Query())
            ->from('doacao')
            ->where(['participacao_id' => $participacaoId])
            ->exists();
    }
}
