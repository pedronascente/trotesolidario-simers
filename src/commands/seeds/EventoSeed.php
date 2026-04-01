<?php

namespace app\commands\seeds;

class EventoSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'evento';
    }

    public function run()
    {
        $this->upsertRows('{{%evento}}', [
            [
                'id' => 1,
                'trote_id' => 1,
                'nome' => 'Mutirao de arrecadacao',
                'data_evento' => '2026-03-15 09:00:00',
            ],
            [
                'id' => 2,
                'trote_id' => 1,
                'nome' => 'Dia de acao presencial',
                'data_evento' => '2026-03-22 14:00:00',
            ],
            [
                'id' => 3,
                'trote_id' => 1,
                'nome' => 'Entrega solidaria',
                'data_evento' => '2026-04-05 10:00:00',
            ],
            [
                'id' => 4,
                'trote_id' => 2,
                'nome' => 'Encerramento historico 2025.2',
                'data_evento' => '2025-10-29 19:00:00',
            ],
        ]);
    }
}