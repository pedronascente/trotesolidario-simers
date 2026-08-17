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
            [['participacao_id'], 'validateParticipacaoAtiva'],
            [
                ['tipo_doacao_id'],
                'exist',
                'targetClass' => TipoDoacao::class,
                'targetAttribute' => ['tipo_doacao_id' => 'id'],
            ],
            [['tipo_doacao_id'], 'validateTipoDoacaoAtivo'],
            [
                ['evento_id'],
                'exist',
                'skipOnEmpty' => true,
                'targetClass' => Evento::class,
                'targetAttribute' => ['evento_id' => 'id'],
            ],
            [['evento_id'], 'validateEventoPertenceParticipacao'],
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
            'participacao_id' => 'Participação',
            'tipo_doacao_id' => 'Tipo de Doação',
            'evento_id' => 'Evento',
            'cpf_snapshot' => 'CPF registrado',
            'edicao_snapshot' => 'Edição registrada',
            'arquivo' => 'Arquivo',
            'file' => 'Comprovante da doação',
            'status' => 'Status',
            'motivo_reprovado' => 'Motivo da reprovação',
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

    public function validateParticipacaoAtiva(string $attribute): void
    {
        if ($this->hasErrors($attribute) || empty($this->participacao_id) || !$this->isNewRecord) {
            return;
        }

        $participacao = $this->participacao;
        if ($participacao === null) {
            $participacao = Participacao::findOne((int) $this->participacao_id);
        }

        if ($participacao !== null && $participacao->status !== Participacao::STATUS_ATIVO) {
            $this->addError($attribute, 'A participacao selecionada nao esta ativa para registrar doacoes.');
        }
    }

    public function validateTipoDoacaoAtivo(string $attribute): void
    {
        if ($this->hasErrors($attribute) || empty($this->tipo_doacao_id)) {
            return;
        }

        $tipoDoacao = $this->tipoDoacao;
        if ($tipoDoacao === null) {
            $tipoDoacao = TipoDoacao::findOne((int) $this->tipo_doacao_id);
        }

        if ($tipoDoacao !== null && (int) $tipoDoacao->ativo !== 1) {
            $this->addError($attribute, 'O tipo de doacao selecionado nao esta mais disponivel.');
        }
    }

    public function validateEventoPertenceParticipacao(string $attribute): void
    {
        if ($this->hasErrors($attribute) || empty($this->evento_id) || empty($this->participacao_id)) {
            return;
        }

        $participacao = $this->participacao;
        if ($participacao === null && $this->participacao_id) {
            $participacao = Participacao::findOne((int) $this->participacao_id);
        }

        if ($participacao === null || empty($participacao->trote_id)) {
            return;
        }

        $evento = $this->evento;
        if ($evento === null && $this->evento_id) {
            $evento = Evento::findOne((int) $this->evento_id);
        }

        if ($evento !== null && (int) $evento->trote_id !== (int) $participacao->trote_id) {
            $this->addError($attribute, 'O evento selecionado nao pertence a participacao informada.');
        }
    }
}
