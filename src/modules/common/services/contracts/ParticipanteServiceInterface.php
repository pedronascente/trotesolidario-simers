<?php

namespace app\modules\common\services\contracts;

use app\modules\common\models\Participante;

interface ParticipanteServiceInterface
{
    public function findById($id);
    public function findByUserId($userId);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getAll($filters = [], $page = 1, $pageSize = 20);
    public function getProfile($id);
    public function isStudent($userId);
    public function getStudentsGraduatingSoon($days = 30);
}
