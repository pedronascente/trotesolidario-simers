<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Participacao;

interface ParticipacaoServiceInterface
{
    public function create(Participacao $model): bool;
    public function update(Participacao $model): bool;
    public function delete(Participacao $model): bool;
    public function findModel(int $id): ?Participacao;
    public function findUsers(): array;
    public function findTrotes(): array;
    public function findUniversidades(): array;
}
