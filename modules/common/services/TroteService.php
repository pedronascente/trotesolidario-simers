<?php

namespace app\modules\common\services;

use Yii;
use app\modules\common\models\Trote;
use app\modules\common\services\contracts\TroteServiceInterface;

class TroteService implements TroteServiceInterface
{
    public function create(Trote $trote): bool{
        return Yii::$app->db->transaction(function () use ($trote) {

            if (!$trote->validate()) {
                return false;
            }

            return $trote->save(false);
        });
    }

    public function update(Trote $trote): bool{
        return Yii::$app->db->transaction(function () use ($trote) {

            if (!$trote->validate()) {
                return false;
            }

            return $trote->save(false);
        });
    }

    public function toggleAtivo(Trote $trote): bool{
        $trote->ativo = !$trote->ativo;
        return $trote->save(false);
    }
}
