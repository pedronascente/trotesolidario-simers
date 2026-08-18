<?php

namespace app\modules\common\models;

use app\models\User;
use yii\db\ActiveRecord;

class ParticipacaoUniversidadeChangeRequest extends ActiveRecord
{
    public const STATUS_PENDENTE = 'pendente';
    public const STATUS_APROVADO = 'aprovado';
    public const STATUS_REJEITADO = 'rejeitado';

    public static function tableName()
    {
        return 'participacao_universidade_change_request';
    }

    public function rules(): array
    {
        return [
            [['participacao_id', 'old_universidade_id', 'new_universidade_id', 'motivo', 'status', 'requested_by', 'created_at'], 'required'],
            [['participacao_id', 'old_universidade_id', 'new_universidade_id', 'requested_by', 'reviewed_by'], 'integer'],
            [['motivo', 'review_notes'], 'string'],
            [['created_at', 'reviewed_at'], 'safe'],
            [['status'], 'string', 'max' => 20],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            [['participacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participacao::class, 'targetAttribute' => ['participacao_id' => 'id']],
            [['old_universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['old_universidade_id' => 'id']],
            [['new_universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['new_universidade_id' => 'id']],
            [['requested_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['requested_by' => 'id']],
            [['reviewed_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reviewed_by' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'participacao_id' => 'Participacao',
            'old_universidade_id' => 'Universidade atual',
            'new_universidade_id' => 'Nova universidade',
            'motivo' => 'Motivo',
            'status' => 'Status',
            'requested_by' => 'Solicitado por',
            'reviewed_by' => 'Revisado por',
            'review_notes' => 'Observacoes da analise',
            'created_at' => 'Solicitado em',
            'reviewed_at' => 'Revisado em',
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_REJEITADO => 'Reprovado',
        ];
    }

    public function getParticipacao()
    {
        return $this->hasOne(Participacao::class, ['id' => 'participacao_id']);
    }

    public function getOldUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'old_universidade_id']);
    }

    public function getNewUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'new_universidade_id']);
    }

    public function getSolicitante()
    {
        return $this->hasOne(User::class, ['id' => 'requested_by']);
    }

    public function getRevisor()
    {
        return $this->hasOne(User::class, ['id' => 'reviewed_by']);
    }

    public function beforeValidate(): bool
    {
        if (!parent::beforeValidate()) {
            return false;
        }

        if ($this->isNewRecord && empty($this->status)) {
            $this->status = self::STATUS_PENDENTE;
        }

        if ($this->isNewRecord && empty($this->created_at)) {
            $this->created_at = date('Y-m-d H:i:s');
        }

        return true;
    }
}
