<?php

namespace app\modules\common\models;

use app\models\User;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Html;


class Participacao extends ActiveRecord
{
    public const STATUS_ATIVO = 'ativo';
    public const STATUS_CANCELADO = 'cancelado';
    public const STATUS_ENCERRADO = 'encerrado';

    public static function tableName()
    {
        return 'participacao';
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

    public function rules()
    {
        return [
            [['user_id', 'trote_id', 'universidade_id', 'curso'], 'required'],
            [['user_id', 'trote_id', 'universidade_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['curso'], 'string', 'max' => 120],
            [['status'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_ATIVO],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            [['user_id', 'trote_id'], 'unique', 'targetAttribute' => ['user_id', 'trote_id'], 'message' => 'Este usuario ja esta vinculado a esta edicao do trote.'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
            [['universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['universidade_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Usuario',
            'trote_id' => 'Trote',
            'universidade_id' => 'Universidade',
            'curso' => 'Curso',
            'status' => 'Status Participação',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public static function getStatusList(): array
    {
        return [
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_CANCELADO => 'Cancelado',
            self::STATUS_ENCERRADO => 'Encerrado',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }

    public function getDoacoes()
    {
        return $this->hasMany(Doacao::class, ['participacao_id' => 'id']);
    }

    public function getCertificados()
    {
        return $this->hasMany(Certificado::class, ['participacao_id' => 'id']);
    }

    public function getDisplayLabel(): string
    {
        $nome = $this->user->nome ?? 'Sem usuario';
        $edicao = $this->trote->edicao ?? 'Sem edicao';
        $universidade = $this->universidade->nome ?? 'Sem universidade';

        return sprintf('%s | %s | %s', $nome, $edicao, $universidade);
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            self::STATUS_ATIVO => Html::tag('span', 'Ativo', ['class' => 'badge badge-success']),
            self::STATUS_CANCELADO => Html::tag('span', 'Cancelado', ['class' => 'badge badge-danger']),
            self::STATUS_ENCERRADO => Html::tag('span', 'Encerrado', ['class' => 'badge badge-secondary']),
            default => Html::tag('span', 'Desconhecido', ['class' => 'badge badge-secondary']),
        };
    }
}
