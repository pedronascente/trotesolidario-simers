<?php

namespace app\commands\seeds;

class RankingCacheSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'ranking-cache';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%ranking_cache}}', [
            [
                'id' => 1,
                'participacao_id' => 1,
                'trote_id' => 1,
                'pontuacao_total' => 180,
                'posicao' => 1,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'participacao_id' => 2,
                'trote_id' => 1,
                'pontuacao_total' => 120,
                'posicao' => 2,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'participacao_id' => 3,
                'trote_id' => 1,
                'pontuacao_total' => 100,
                'posicao' => 3,
                'updated_at' => $now,
            ],
        ]);
    }
}