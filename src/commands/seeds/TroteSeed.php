<?php

namespace app\commands\seeds;

class TroteSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'trote';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%trote}}', [
            [
                'id' => 1,
                'titulo' => 'Trote Solidario 2026.1',
                'descricao' => 'Campanha ativa com participacoes, eventos, doacoes e ranking consolidado.',
                'edicao' => '2026.1',
                'status' => 'ativo',
                'data_inicio' => '2026-02-01',
                'data_fim' => '2026-04-30',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'titulo' => 'Trote Solidario 2025.2',
                'descricao' => 'Edicao encerrada usada para historico e testes de visualizacao.',
                'edicao' => '2025.2',
                'status' => 'encerrado',
                'data_inicio' => '2025-09-15',
                'data_fim' => '2025-10-29',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}