<?php

namespace app\modules\common\services;

use app\models\User;
use app\modules\common\models\Participante;
use app\modules\common\services\contracts\ParticipanteServiceInterface;
use yii\db\Query;
use yii\web\NotFoundHttpException;

class ParticipanteService implements ParticipanteServiceInterface
{
    public function findById($id)
    {
        return Participante::find()->with(['user'])->where(['id' => $id])->one();
    }

    public function findByUserId($userId)
    {
        return Participante::find()->with(['user'])->where(['user_id' => $userId])->one();
    }

    public function create(array $data)
    {
        $participante = new Participante();
        $participante->attributes = $data;
        $this->ensureValidUser($participante->user_id);

        if (Participante::find()->where(['user_id' => $participante->user_id])->exists()) {
            throw new \RuntimeException('Este usuario ja possui perfil de participante.');
        }

        if (!$participante->save()) {
            throw new \RuntimeException('Erro ao criar participante: ' . json_encode($participante->errors));
        }

        return $this->findById($participante->id);
    }

    public function update($id, array $data)
    {
        $participante = Participante::findOne($id);

        if ($participante === null) {
            throw new NotFoundHttpException('Participante nao encontrado.');
        }

        $newUserId = $data['user_id'] ?? $participante->user_id;
        $this->ensureValidUser($newUserId);

        $existing = Participante::find()->where(['user_id' => $newUserId])->andWhere(['<>', 'id', $participante->id])->exists();
        if ($existing) {
            throw new \RuntimeException('Este usuario ja possui perfil de participante.');
        }

        $participante->attributes = $data;

        if (!$participante->save()) {
            throw new \RuntimeException('Erro ao atualizar participante: ' . json_encode($participante->errors));
        }

        return $this->findById($participante->id);
    }

    public function delete($id)
    {
        $participante = Participante::findOne($id);

        if ($participante === null) {
            throw new NotFoundHttpException('Participante nao encontrado.');
        }

        if ($this->hasRelatedRecords($participante->user_id)) {
            throw new \RuntimeException('Nao e possivel excluir este participante porque existem participacoes vinculadas ao usuario.');
        }

        return $participante->delete() > 0;
    }

    public function getAll($filters = [], $page = 1, $pageSize = 20)
    {
        $query = Participante::find()->with(['user'])->joinWith(['user']);

        if (isset($filters['estudante']) && $filters['estudante'] !== '') {
            $query->andWhere(['participante.estudante' => (int) $filters['estudante']]);
        }

        if (isset($filters['estudante_medicina']) && $filters['estudante_medicina'] !== '') {
            $query->andWhere(['participante.estudante_medicina' => (int) $filters['estudante_medicina']]);
        }

        if (isset($filters['user_status']) && $filters['user_status'] !== '') {
            $query->andWhere(['user.status' => (int) $filters['user_status']]);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->andWhere(['or', ['like', 'user.nome', $search], ['like', 'user.email', $search], ['like', 'user.cpf', preg_replace('/\D/', '', (string) $search)]]);
        }

        if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
            $query->andWhere(['between', 'participante.previsao_formatura', $filters['data_inicio'], $filters['data_fim']]);
        }

        $query->orderBy(['participante.id' => SORT_DESC]);

        $total = (int) $query->count();
        $data = $query->offset(($page - 1) * $pageSize)->limit($pageSize)->all();

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => (int) ceil($total / $pageSize),
        ];
    }

    public function getProfile($id)
    {
        $participante = $this->findById($id);

        if ($participante === null) {
            throw new NotFoundHttpException('Participante nao encontrado.');
        }

        return [
            'participante' => $participante,
            'user' => $participante->user,
            'estatisticas' => [
                'total_participacoes' => (new Query())->from('participacao')->where(['user_id' => $participante->user_id])->count(),
            ],
        ];
    }

    public function isStudent($userId)
    {
        $participante = Participante::findOne(['user_id' => $userId]);
        return $participante ? (bool) $participante->estudante : false;
    }

    public function getStudentsGraduatingSoon($days = 30)
    {
        $today = date('Y-m-d H:i:s');
        $limitDate = date('Y-m-d H:i:s', strtotime('+' . (int) $days . ' days'));

        return Participante::find()
            ->with(['user'])
            ->where(['estudante' => 1])
            ->andWhere(['between', 'previsao_formatura', $today, $limitDate])
            ->orderBy(['previsao_formatura' => SORT_ASC])
            ->all();
    }

    private function ensureValidUser($userId): void
    {
        if (empty($userId) || !User::find()->where(['id' => $userId])->exists()) {
            throw new \RuntimeException('Usuario nao encontrado.');
        }
    }

    private function hasRelatedRecords($userId): bool
    {
        return (new Query())
            ->from('participacao')
            ->where(['user_id' => $userId])
            ->exists();
    }
}
