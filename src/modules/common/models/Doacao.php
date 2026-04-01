<?php

namespace app\modules\common\models;

use app\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Doacao extends ActiveRecord
{
    public $file;
    public $participacao_label;
    public $evento_nome;
    public $tipo_doacao_nome;

    const STATUS_PENDENTE = 'pendente';
    const STATUS_APROVADA = 'aprovada';
    const STATUS_REJEITADA = 'rejeitada';

    public static function tableName()
    {
        return 'doacao';
    }

    public function rules()
    {
        return [
            [['participacao_id', 'tipo_doacao_id'], 'required'],
            [['participacao_id', 'tipo_doacao_id', 'evento_id', 'validado_por'], 'integer'],
            [['motivo_reprovado'], 'string'],
            [['validado_em', 'created_at', 'updated_at'], 'safe'],
            [['cpf_snapshot'], 'string', 'max' => 14],
            [['edicao_snapshot'], 'string', 'max' => 10],
            [['arquivo'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_PENDENTE],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            [
                ['file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'pdf'],
                'maxSize' => 1024 * 1024 * 5,
            ],
            [
                ['arquivo'],
                'required',
                'when' => function ($model) {
                    return $model->isNewRecord && empty($model->arquivo) && $model->file === null;
                },
                'message' => 'Envie um arquivo para a doacao.',
            ],
            [
                ['participacao_id'],
                'exist',
                'targetClass' => Participacao::class,
                'targetAttribute' => ['participacao_id' => 'id'],
            ],
            [
                ['tipo_doacao_id'],
                'exist',
                'targetClass' => TipoDoacao::class,
                'targetAttribute' => ['tipo_doacao_id' => 'id'],
            ],
            [
                ['evento_id'],
                'exist',
                'skipOnEmpty' => true,
                'targetClass' => Evento::class,
                'targetAttribute' => ['evento_id' => 'id'],
            ],
            [
                ['validado_por'],
                'exist',
                'skipOnEmpty' => true,
                'targetClass' => User::class,
                'targetAttribute' => ['validado_por' => 'id'],
            ],
            [
                ['motivo_reprovado'],
                'required',
                'when' => function ($model) {
                    return $model->status === self::STATUS_REJEITADA;
                },
                'whenClient' => "function () { return $('#doacao-status').val() === 'rejeitada'; }",
            ],
            [
                ['cpf_snapshot', 'edicao_snapshot', 'tipo_doacao_id'],
                'unique',
                'targetAttribute' => ['cpf_snapshot', 'edicao_snapshot', 'tipo_doacao_id'],
                'message' => 'Ja existe uma doacao deste tipo para este CPF na edicao informada.',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'participacao_id' => 'Participacao',
            'tipo_doacao_id' => 'Tipo de Doacao',
            'evento_id' => 'Evento',
            'cpf_snapshot' => 'CPF registrado',
            'edicao_snapshot' => 'Edicao registrada',
            'arquivo' => 'Arquivo',
            'file' => 'Arquivo',
            'status' => 'Status',
            'motivo_reprovado' => 'Motivo da reprovacao',
            'validado_por' => 'Validado por',
            'validado_em' => 'Validado em',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function getParticipacao()
    {
        return $this->hasOne(Participacao::class, ['id' => 'participacao_id']);
    }

    public function getEvento()
    {
        return $this->hasOne(Evento::class, ['id' => 'evento_id']);
    }

    public function getTipoDoacao()
    {
        return $this->hasOne(TipoDoacao::class, ['id' => 'tipo_doacao_id']);
    }

    public function getValidador()
    {
        return $this->hasOne(User::class, ['id' => 'validado_por']);
    }

    public function getParticipacaoDisplay(): string
    {
        return $this->participacao ? $this->participacao->getDisplayLabel() : '-';
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_PENDENTE => 'Pendente',
            self::STATUS_APROVADA => 'Aprovada',
            self::STATUS_REJEITADA => 'Rejeitada',
        ];
    }
}
