<?php

namespace app\modules\common\models;

use app\modules\common\models\Doacao;
use app\modules\common\models\Evento;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Trote extends ActiveRecord
{
    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_ATIVO = 'ativo';
    const STATUS_ENCERRADO = 'encerrado';
    const ATIVO_SIM = 1;
    const ATIVO_NAO = 0;

    public static function tableName()
    {
        return '{{%trote}}';
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

            // Obrigatórios
            [['titulo', 'numero_edicao', 'ano', 'status', 'data_inicio', 'data_fim', 'descricao'], 'required'],

            // Inteiros
            [['numero_edicao', 'ano', 'ativo'], 'integer'],

            // Texto
            [['descricao'], 'string'],

            // Datas (apenas data)
            [['data_inicio', 'data_fim'], 'date', 'format' => 'php:Y-m-d'],

            // Strings
            [['titulo'], 'string', 'max' => 255],
            [['status'], 'string', 'max' => 20],

            // Status permitido
            ['status', 'in', 'range' => array_keys(self::getStatusList())],

            // Default
            ['status', 'default', 'value' => self::STATUS_RASCUNHO],
            ['ativo', 'default', 'value' => self::ATIVO_SIM],

            // Unique composta
            [
                ['numero_edicao', 'ano'],
                'unique',
                'targetAttribute' => ['numero_edicao', 'ano'],
                'message' => 'Já existe um trote cadastrado para essa edição e ano.'
            ],

            // Validação personalizada de datas
            ['data_fim', 'validateDatas'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Título',
            'numero_edicao' => 'Nº Edição',
            'ano' => 'Ano',
            'descricao' => 'Descrição',
            'status' => 'Status',
            'data_inicio' => 'Data Início',
            'data_fim' => 'Data Fim',
            'ativo' => 'Ativo',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_RASCUNHO  => 'Rascunho',
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_ENCERRADO => 'Encerrado',
        ];
    }

    /**
     * Retorna edição formatada (02/2026)
     */
    public function getEdicaoFormatada()
    {
        return str_pad($this->numero_edicao, 2, '0', STR_PAD_LEFT) . '/' . $this->ano;
    }

    public function getStatusLabel()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function isAtivo()
    {
        return $this->ativo == self::ATIVO_SIM;
    }

    /**
     * Validação: data fim não pode ser menor que início
     */
    public function validateDatas($attribute)
    {
        if ($this->data_inicio && $this->data_fim) {
            if (strtotime($this->data_fim) < strtotime($this->data_inicio)) {
                $this->addError($attribute, 'A data fim não pode ser menor que a data início.');
            }
        }
    }

    public function getRankingCaches()
    {
        return $this->hasMany(RankingCache::class, ['trote_id' => 'id']);
    }

    public function getCertificados()
    {
        return $this->hasMany(Certificado::class, ['trote_id' => 'id']);
    }

    public function getEventos()
    {
        return $this->hasMany(Evento::class, ['trote_id' => 'id']);
    }

    public function getDoacoes()
    {
        return $this->hasMany(Doacao::class, ['trote_id' => 'id']);
    }

    public static function getAtivos()
    {
        return self::find()
            ->where(['ativo' => 1])
            ->orderBy('titulo')
            ->all();
    }

    public function possuiVinculos(): bool
    {
        return
            $this->getRankingCaches()->exists()
            || $this->getCertificados()->exists()
            || $this->getEventos()->exists()
            || $this->getDoacoes()->exists();
    }
}
