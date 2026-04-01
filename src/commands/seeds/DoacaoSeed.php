<?php

namespace app\commands\seeds;

class DoacaoSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'doacao';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%doacao}}', [
            [
                'id' => 1,
                'participacao_id' => 1,
                'tipo_doacao_id' => 1,
                'evento_id' => 1,
                'cpf_snapshot' => '111.444.777-35',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/doacao-sangue-ana.pdf',
                'status' => 'aprovada',
                'motivo_reprovado' => null,
                'validado_por' => 1,
                'validado_em' => '2026-03-16 10:30:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'participacao_id' => 1,
                'tipo_doacao_id' => 4,
                'evento_id' => 2,
                'cpf_snapshot' => '111.444.777-35',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/presencial-ana.pdf',
                'status' => 'aprovada',
                'motivo_reprovado' => null,
                'validado_por' => 1,
                'validado_em' => '2026-03-23 08:45:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'participacao_id' => 1,
                'tipo_doacao_id' => 2,
                'evento_id' => 3,
                'cpf_snapshot' => '111.444.777-35',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/alimentos-ana.pdf',
                'status' => 'rejeitada',
                'motivo_reprovado' => 'Comprovante ilegivel.',
                'validado_por' => 1,
                'validado_em' => '2026-04-06 11:15:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'participacao_id' => 2,
                'tipo_doacao_id' => 3,
                'evento_id' => 1,
                'cpf_snapshot' => '123.456.789-09',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/comissao-bruno.pdf',
                'status' => 'aprovada',
                'motivo_reprovado' => null,
                'validado_por' => 1,
                'validado_em' => '2026-03-17 16:20:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 5,
                'participacao_id' => 2,
                'tipo_doacao_id' => 2,
                'evento_id' => 3,
                'cpf_snapshot' => '123.456.789-09',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/alimentos-bruno.pdf',
                'status' => 'pendente',
                'motivo_reprovado' => null,
                'validado_por' => null,
                'validado_em' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 6,
                'participacao_id' => 3,
                'tipo_doacao_id' => 5,
                'evento_id' => 1,
                'cpf_snapshot' => '987.654.321-00',
                'edicao_snapshot' => '2026.1',
                'arquivo' => 'comprovantes/medula-carla.pdf',
                'status' => 'aprovada',
                'motivo_reprovado' => null,
                'validado_por' => 1,
                'validado_em' => '2026-03-18 13:10:00',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}