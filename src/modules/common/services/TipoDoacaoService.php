<?php

namespace app\modules\common\services;

use Yii;
use app\modules\common\models\TipoDoacao;
use app\modules\common\services\contracts\TipoDoacaoServiceInterface;

class TipoDoacaoService implements TipoDoacaoServiceInterface
{
    public function create(TipoDoacao $tipoDoacao): bool
    {
        return Yii::$app->db->transaction(function () use ($tipoDoacao) {
            if (!$tipoDoacao->validate()) {
                return false;
            }
            return $tipoDoacao->save(false);
        });
    }

    public function update(TipoDoacao $tipoDoacao): bool
    {
        return Yii::$app->db->transaction(function () use ($tipoDoacao) {
            if (!$tipoDoacao->validate()) {
                return false;
            }
            return $tipoDoacao->save(false);
        });
    }

    public function delete(TipoDoacao $tipoDoacao): bool
    {
        return Yii::$app->db->transaction(function () use ($tipoDoacao) {
            return $tipoDoacao->delete() !== false;
        });
    }
}
