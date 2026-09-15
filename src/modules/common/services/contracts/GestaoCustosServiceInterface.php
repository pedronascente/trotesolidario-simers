<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\CategoriaCusto;

interface GestaoCustosServiceInterface
{
    public function save(CategoriaCusto $categoria, array $distribuicoes): bool;
    public function delete(CategoriaCusto $categoria): bool;
    public function findModel(int $id): ?CategoriaCusto;
    public function getTrotes(): array;
    public function getUniversidades(array $incluirIds = []): array;
    public function getTiposCategoria(array $incluirIds = []): array;
    public function getDashboard(?int $troteId): array;
}
