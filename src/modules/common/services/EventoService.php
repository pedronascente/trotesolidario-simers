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
                throw new Exception('Erro ao salvar Evento.');
            }

            return true;
        });
    }

    public function update($model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Não é possível atualizar um evento não persistido.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if (!$model->validate()) {
                return false;
            }

            if (!$model->save(false)) {
                throw new Exception('Erro ao atualizar Evento.');
            }

            return true;
        });
    }

    public function delete($model): bool
    {
        if ($model->isNewRecord) {
            throw new Exception('Não é possível excluir um evento não persistido.');
        }

        return Yii::$app->db->transaction(function () use ($model) {
            if ($model->delete() === false) {
                throw new Exception('Erro ao excluir Evento.');
            }

            return true;
        });
    }

    public function findModel(int $id): ?Evento
    {
        return Evento::findOne($id);
    }

    public function findTrotes()
    {
        return  ArrayHelper::map(
            Trote::find()->orderBy('titulo')->all(),
            'id',
            'titulo'
        );
    }
}