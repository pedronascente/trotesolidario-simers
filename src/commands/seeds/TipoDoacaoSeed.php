<?php

namespace app\commands\seeds;

class TipoDoacaoSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'tipo-doacao';
    }

    public function run()
    {
        $this->upsertRows('{{%tipo_doacao}}', [
            [
                'id' => 1,
                'nome' => 'Sangue',
                'descricao' => 'Doacao de sangue validada pela organizacao.',
                'carga_horaria' => 4,
                'pontuacao_ranking' => 100,
                'ativo' => 1,
            ],
            [
                'id' => 2,
                'nome' => 'Alimentos',
                'descricao' => 'Entrega de alimentos nao pereciveis.',
                'carga_horaria' => 2,
                'pontuacao_ranking' => 60,
                'ativo' => 1,
            ],
            [
                'id' => 3,
                'nome' => 'Comissão',
                'descricao' => 'Atuacao na comissao organizadora da campanha.',
                'carga_horaria' => 4,
                'pontuacao_ranking' => 120,
                'ativo' => 1,
            ],
            [
                'id' => 4,
                'nome' => 'Participação Presencial',
                'descricao' => 'Participacao presencial em acao oficial do trote.',
                'carga_horaria' => 4,
                'pontuacao_ranking' => 80,
                'ativo' => 1,
            ],
            [
                'id' => 5,
                'nome' => 'Medula Óssea',
                'descricao' => 'Cadastro ou comprovacao de doacao relacionada a medula ossea.',
                'carga_horaria' => 4,
                'pontuacao_ranking' => 100,
                'ativo' => 1,
            ],
        ]);
    }
}