<?php

namespace app\modules\common\services;

use Yii;
use app\modules\common\models\TipoDoacao;
use app\modules\common\services\contracts\RankingCacheServiceInterface;
use app\modules\common\services\contracts\TipoDoacaoServiceInterface;

class TipoDoacaoService implements TipoDoacaoServiceInterface
{
    private RankingCacheServiceInterface $rankingCacheService;

    public function __construct(RankingCacheServiceInterface $rankingCacheService)
    {
        $this->rankingCacheService = $rankingCacheService;
    }

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
            $oldPontuacaoRanking = (int) $tipoDoacao->getOldAttribute('pontuacao_ranking');

            if (!$tipoDoacao->validate()) {
                return false;
            }

            $saved = $tipoDoacao->save(false);
            if ($saved && $oldPontuacaoRanking !== (int) $tipoDoacao->pontuacao_ranking) {
                $this->rankingCacheService->rebuild();
            }

            return $saved;
        });
    }

    public function delete(TipoDoacao $tipoDoacao): bool
    {
        return Yii::$app->db->transaction(function () use ($tipoDoacao) {
            $deleted = $tipoDoacao->delete() !== false;
            if ($deleted) {
                $this->rankingCacheService->rebuild();
            }

            return $deleted;
        });
    }
}
