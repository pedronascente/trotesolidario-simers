<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Doacao;

interface DoacaoServiceInterface
{
    public function create(Doacao $doacao): bool;

    public function update(Doacao $doacao): bool;

    public function aprovar(Doacao $doacao, int $adminId): bool;

    public function rejeitar(Doacao $doacao, int $adminId): bool;
}
