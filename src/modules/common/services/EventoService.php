<?php

namespace app\modules\common\services;

use app\modules\common\models\Evento;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\EventoServiceInterface;
use Yii;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

class EventoService implements EventoServiceInterface
{
    public function create($model): bool
    {
        return Yii::$app->db->transaction(function () use ($model) {
            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao salvar evento.');
            }

            return true;
        });
    }

    public function update($model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel atualizar um evento nao persistido.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao atualizar evento.');
            }

            return true;
        });
    }

    public function delete($model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Nao e possivel excluir um evento nao persistido.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if ($model->delete() === false) {
                throw new Exception('Erro ao excluir evento.');
            }

            return true;
        });
    }

    public function findModel(int $id): ?Evento
    {
        return Evento::findOne($id);
    }

    public function findTrotes(): array
    {
        $trotes = Trote::find()->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])->all();

        return ArrayHelper::map($trotes, 'id', function (Trote $trote) {
            $titulo = $trote->titulo ?: 'Sem titulo';
            $edicao = $trote->edicao ?: 'Sem edicao';
            return $titulo . ' | ' . $edicao;
        });
    }
}
