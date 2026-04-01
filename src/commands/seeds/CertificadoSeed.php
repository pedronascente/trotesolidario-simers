<?php

namespace app\commands\seeds;

class CertificadoSeed extends BaseSeed
{
    public static function seedName()
    {
        return 'certificado';
    }

    public function run()
    {
        $this->upsertRows('{{%certificado}}', [
            [
                'id' => 1,
                'participacao_id' => 1,
                'codigo_validador' => 'CERT-2026-ANA-001',
                'carga_horaria_total' => 8,
                'arquivo_pdf' => 'pdf/certificados/certificado-participacao-1.pdf',
                'data_emissao' => '2026-03-25 15:00:00',
                'hash_integridade' => hash('sha256', 'CERT-2026-ANA-001'),
                'emitido_por' => 1,
            ],
            [
                'id' => 2,
                'participacao_id' => 2,
                'codigo_validador' => 'CERT-2026-BRUNO-001',
                'carga_horaria_total' => 4,
                'arquivo_pdf' => 'pdf/certificados/certificado-participacao-2.pdf',
                'data_emissao' => '2026-03-26 11:30:00',
                'hash_integridade' => hash('sha256', 'CERT-2026-BRUNO-001'),
                'emitido_por' => 1,
            ],
            [
                'id' => 3,
                'participacao_id' => 3,
                'codigo_validador' => 'CERT-2026-CARLA-001',
                'carga_horaria_total' => 4,
                'arquivo_pdf' => 'pdf/certificados/certificado-participacao-3.pdf',
                'data_emissao' => '2026-03-27 09:10:00',
                'hash_integridade' => hash('sha256', 'CERT-2026-CARLA-001'),
                'emitido_por' => 1,
            ],
        ]);
    }
}