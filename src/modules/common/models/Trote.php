<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

class Trote extends ActiveRecord
{
    const STATUS_RASCUNHO = 'rascunho';
    const STATUS_ATIVO = 'ativo';
    const STATUS_ENCERRADO = 'encerrado';

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
            [['titulo', 'edicao', 'data_inicio', 'data_fim'], 'required'],
            [['titulo', 'edicao', 'descricao', 'status'], 'trim'],
            [['descricao'], 'string'],
            [['data_inicio', 'data_fim'], 'date', 'format' => 'php:Y-m-d'],
            [['titulo'], 'string', 'min' => 2, 'max' => 200],
            [['edicao'], 'string', 'length' => 6],
            ['edicao', 'match', 'pattern' => '/^\d{4}\.\d$/', 'message' => 'A edicao deve seguir o formato 0000.9.'],
            [['status'], 'string', 'max' => 20],
            ['status', 'default', 'value' => self::STATUS_RASCUNHO],
            ['status', 'in', 'range' => array_keys(self::getStatusList())],
            ['edicao', 'unique', 'message' => 'Ja existe um trote cadastrado para esta edicao.'],
            ['data_fim', 'validateDatas'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'edicao' => 'Edicao',
            'descricao' => 'Descricao',
            'status' => 'Status',
            'data_inicio' => 'Data Inicio',
            'data_fim' => 'Data Fim',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public static function getStatusList()
    {
        return [
            self::STATUS_RASCUNHO => 'Rascunho',
            self::STATUS_ATIVO => 'Ativo',
            self::STATUS_ENCERRADO => 'Encerrado',
        ];
    }

    public function getEdicaoFormatada()
    {
        return $this->edicao ?: '-';
    }

    public function getStatusLabel()
    {
        return self::getStatusList()[$this->status] ?? '-';
    }

    public function isAtivo()
    {
        return $this->status === self::STATUS_ATIVO;
    }

    public function validateDatas($attribute)
    {
        if ($this->data_inicio && $this->data_fim && strtotime($this->data_fim) < strtotime($this->data_inicio)) {
            $this->addError('data_inicio', 'A data inicio nao pode ser maior que a data fim.');
            $this->addError('data_fim', 'A data fim nao pode ser menor que a data inicio.');
        }
    }

    public static function getAtivos()
    {
        return self::find()
            ->where(['status' => self::STATUS_ATIVO])
            ->orderBy(['titulo' => SORT_ASC, 'edicao' => SORT_DESC])
            ->all();
    }

    public function possuiVinculos(): bool
    {
        $db = Yii::$app->db;

        $temParticipacao = (new Query())
            ->from('{{%participacao}}')
            ->where(['trote_id' => $this->id])
            ->exists($db);

        if ($temParticipacao) {
            return true;
        }

        $temEvento = (new Query())
            ->from('{{%evento}}')
            ->where(['trote_id' => $this->id])
            ->exists($db);

        if ($temEvento) {
            return true;
        }

        $temRanking = (new Query())
            ->from('{{%ranking_cache}}')
            ->where(['trote_id' => $this->id])
            ->exists($db);

        return $temRanking;
    }

    public function getEventos()
    {
        return $this->hasMany(Evento::class, ['trote_id' => 'id'])->orderBy(['data_evento' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
