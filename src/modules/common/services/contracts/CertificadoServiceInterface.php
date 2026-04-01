<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Certificado;
use app\modules\common\models\Doacao;

interface CertificadoServiceInterface
{
    public function syncFromApprovedDoacao(Doacao $doacao, int $adminUserId): Certificado;

    public function ensurePdf(Certificado $certificado, bool $force = false): Certificado;
}
