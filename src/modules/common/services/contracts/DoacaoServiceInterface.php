<?php

namespace app\modules\common\services\contracts;

interface DoacaoServiceInterface
{
    public function create($model): bool;
    public function update($model): bool;
    public function getFormData(): array;
    public function getEventosByParticipacao(int $participacaoId): array;
    public function aprovar(int $id): bool;
    public function reprovar(int $id, string $motivoReprovado): bool;
}
