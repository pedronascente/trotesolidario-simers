<?php

namespace app\commands\seeds;

class ParticipacaoSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'participacao';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%participacao}}', [
            [
                'id' => 1,
                'user_id' => 2,
                'trote_id' => 1,
                'universidade_id' => 1,
                'curso' => 'Medicina',
                'status' => 'ativo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'trote_id' => 1,
                'universidade_id' => 2,
                'curso' => 'Enfermagem',
                'status' => 'ativo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'trote_id' => 1,
                'universidade_id' => 3,
                'curso' => 'Medicina',
                'status' => 'ativo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}