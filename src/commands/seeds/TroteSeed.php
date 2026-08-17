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
                'titulo' => 'Trote Solidario 2021',
                'descricao' => 'Campanha ativa com participacoes, eventos, doacoes e ranking consolidado.',
                'edicao' => '2021.1',
                'status' => 'ativo',
                'data_inicio' => '2021-02-01',
                'data_fim' => '2021-04-30',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'titulo' => 'Trote Solidario 2022',
                'descricao' => 'Campanha ativa com participacoes, eventos, doacoes e ranking consolidado.',
                'edicao' => '2022.1',
                'status' => 'ativo',
                'data_inicio' => '2022-02-01',
                'data_fim' => '2022-04-30',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'titulo' => 'Trote Solidario 2023',
                'descricao' => 'Campanha ativa com participacoes, eventos, doacoes e ranking consolidado.',
                'edicao' => '2023.1',
                'status' => 'ativo',
                'data_inicio' => '2023-02-01',
                'data_fim' => '2023-04-30',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'titulo' => 'Trote Solidario 2024',
                'descricao' => 'Edicao encerrada usada para historico e testes de visualizacao.',
                'edicao' => '2024.2',
                'status' => 'encerrado',
                'data_inicio' => '2024-09-15',
                'data_fim' => '2024-10-29',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'titulo' => 'Trote Solidario 2025',
                'descricao' => 'Edicao encerrada usada para historico e testes de visualizacao.',
                'edicao' => '2025.1',
                'status' => 'encerrado',
                'data_inicio' => '2025-09-15',
                'data_fim' => '2025-10-29',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'titulo' => 'Trote Solidario 2026',
                'descricao' => 'Edicao encerrada usada para historico e testes de visualizacao.',
                'edicao' => '2026.1',
                'status' => 'encerrado',
                'data_inicio' => '2026-09-15',
                'data_fim' => '2026-10-29',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}