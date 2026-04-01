<?php

namespace app\commands\seeds;

class DocumentosSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'documentos';
    }

    public function run()
    {
        $now = $this->now();

        $this->upsertRows('{{%documentos}}', [
            [
                'id' => 1,
                'nome' => 'Regulamento Trote Solidario 2026.1',
                'arquivo' => 'documentos/regulamento-2026-1.pdf',
                'tipo' => 'pdf',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nome' => 'Checklist de participacao',
                'arquivo' => 'documentos/checklist-participacao.pdf',
                'tipo' => 'pdf',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nome' => 'Guia rapido do participante',
                'arquivo' => 'documentos/guia-participante.pdf',
                'tipo' => 'pdf',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}