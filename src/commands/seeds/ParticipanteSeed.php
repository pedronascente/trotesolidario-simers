<?php

namespace app\commands\seeds;

class ParticipanteSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'participante';
    }

    public function run()
    {
        $this->upsertRows('{{%participante}}', [
            [
                'id' => 1,
                'user_id' => 2,
                'estudante' => 1,
                'estudante_medicina' => 1,
                'previsao_formatura' => '2027-12-15 00:00:00',
            ],
            [
                'id' => 2,
                'user_id' => 3,
                'estudante' => 1,
                'estudante_medicina' => 0,
                'previsao_formatura' => '2028-07-20 00:00:00',
            ],
            [
                'id' => 3,
                'user_id' => 4,
                'estudante' => 1,
                'estudante_medicina' => 1,
                'previsao_formatura' => '2026-12-20 00:00:00',
            ],
        ]);
    }
}