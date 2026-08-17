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
                'nome' => 'RELAÇÃO DE HEMOCENTROS',
                'arquivo' => '69e26d906f2be.pdf',
                'tipo' => 'informativo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 2,
                'nome' => 'RELAÇÃO DE TAMPINHAS',
                'arquivo' => '69e26dffb6d97.pdf',
                'tipo' => 'informativo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 3,
                'nome' => 'Para ver o regulamento do interior versão.',
                'arquivo' => '69ebb1b29db45.pdf',
                'tipo' => 'regulamento',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => 4,
                'nome' => 'Para ver o regulamento POA + Metropolitana.',
                'arquivo' => '69ebb1d9c07e4.pdf',
                'tipo' => 'regulamento',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}