<?php

namespace app\modules\common\services\contracts;

interface RankingCacheServiceInterface
{
    public function findTrotes(): array;
    public function rebuild(?int $troteId = null): int;
    public function getUniversityRanking(?int $troteId = null): array;
}
