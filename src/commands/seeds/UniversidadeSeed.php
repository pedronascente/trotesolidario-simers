<?php

namespace app\commands\seeds;

class UniversidadeSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'universidade';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%universidade}}', [
            [
                'id' => 1,
                'nome' => 'Universidade Federal de Uberlandia',
                'cidade' => 'Uberlandia',
                'uf' => 'MG',
                'icon' => 'ufu.png',
                'link_doacao_alimento' => 'https://trotesolidario.test/doacao/ufu',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nome' => 'Universidade de Sao Paulo',
                'cidade' => 'Sao Paulo',
                'uf' => 'SP',
                'icon' => 'usp.png',
                'link_doacao_alimento' => 'https://trotesolidario.test/doacao/usp',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nome' => 'Pontificia Universidade Catolica do Rio Grande do Sul',
                'cidade' => 'Porto Alegre',
                'uf' => 'RS',
                'icon' => 'pucrs.png',
                'link_doacao_alimento' => 'https://trotesolidario.test/doacao/pucrs',
                'ativo' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}