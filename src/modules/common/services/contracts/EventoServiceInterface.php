<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Evento;

interface EventoServiceInterface
{
    public function create($model): bool;
    public function update($model): bool;
    public function delete($model): bool;
    public function findModel(int $id): ?Evento;
    public function findTrotes();
} 