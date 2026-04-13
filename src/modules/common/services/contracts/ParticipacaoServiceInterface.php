<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Participacao;
use app\modules\common\models\ParticipacaoUniversidadeChangeRequest;
use app\modules\common\models\ParticipantUniversityCorrectionForm;
use app\modules\common\models\ParticipantUniversityCorrectionRequestForm;

interface ParticipacaoServiceInterface
{
    public function create(Participacao $model): bool;
    public function update(Participacao $model): bool;
    public function delete(Participacao $model): bool;
    public function findModel(int $id): ?Participacao;
    public function findUsers(): array;
    public function findTrotes(): array;
    public function findUniversidades(): array;
    public function findActiveParticipationsByUserId(int $userId): array;
    public function getParticipantUniversitySelfCorrectionData(int $userId, ?int $participacaoId = null): array;
    public function selfCorrectParticipantUniversity(int $userId, int $participacaoId, ParticipantUniversityCorrectionForm $form): Participacao;
    public function getParticipantUniversityCorrectionRequestData(int $userId, ?int $participacaoId = null): array;
    public function submitParticipantUniversityCorrectionRequest(int $userId, int $participacaoId, ParticipantUniversityCorrectionRequestForm $form): ParticipacaoUniversidadeChangeRequest;
    public function findUniversityCorrectionRequest(int $id): ?ParticipacaoUniversidadeChangeRequest;
    public function findUniversityCorrectionRequests(?string $status = null, int $limit = 100): array;
    public function approveUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest;
    public function rejectUniversityCorrectionRequest(int $requestId, int $reviewedBy, ?string $reviewNotes = null): ParticipacaoUniversidadeChangeRequest;
}
