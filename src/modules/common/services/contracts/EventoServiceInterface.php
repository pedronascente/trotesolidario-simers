<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Evento;

interface EventoServiceInterface
{
    public function create(Evento $evento): bool;
    public function update(Evento $evento): bool;
    public function delete(Evento $evento): bool;
    public function findModel(int $id): ?Evento;
}