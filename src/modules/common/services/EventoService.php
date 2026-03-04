<?php

namespace app\modules\common\services;

use Yii;
use app\modules\common\models\Evento;
use app\modules\common\services\contracts\EventoServiceInterface;
use yii\db\Exception;

class EventoService implements EventoServiceInterface
{
    public function create(Evento $evento): bool
    {
        return Yii::$app->db->transaction(function () use ($evento) {
            if (!$evento->validate()) {
                return false;
            }

            if (!$evento->save(false)) {
                throw new Exception('Erro ao salvar Evento.');
            }

            return true;
        });
    }

    public function update(Evento $evento): bool
    {
        if ($evento->isNewRecord) {
            throw new Exception('Não é possível atualizar um evento não persistido.');
        }

        return Yii::$app->db->transaction(function () use ($evento) {
            if (!$evento->validate()) {
                return false;
            }

            if (!$evento->save(false)) {
                throw new Exception('Erro ao atualizar Evento.');
            }

            return true;
        });
    }

    public function delete(Evento $evento): bool
    {
        if ($evento->isNewRecord) {
            throw new Exception('Não é possível excluir um evento não persistido.');
        }

        return Yii::$app->db->transaction(function () use ($evento) {
            if ($evento->delete() === false) {
                throw new Exception('Erro ao excluir Evento.');
            }

            return true;
        });
    }

    public function findModel(int $id): ?Evento
    {
        return Evento::findOne($id);
    }
}