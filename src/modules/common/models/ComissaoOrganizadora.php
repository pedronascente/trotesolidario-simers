<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

class ComissaoOrganizadora extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%comissao_organizadora}}';
    }

    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => new Expression('NOW()'),
        ]];
    }

    public function rules()
    {
        return [
            [['trote_id', 'universidade_id', 'nome'], 'required'],
            [['trote_id', 'universidade_id', 'ordem', 'ativo'], 'integer'],
            [['nome', 'cargo'], 'trim'],
            [['nome'], 'string', 'max' => 180],
            [['cargo'], 'string', 'max' => 120],
            [['ordem'], 'default', 'value' => 0],
            [['ordem'], 'integer', 'min' => 0],
            [['ativo'], 'default', 'value' => 1],
            [['ativo'], 'in', 'range' => [0, 1]],
            [['universidade_id'], 'exist', 'skipOnError' => true, 'targetClass' => Universidade::class, 'targetAttribute' => ['universidade_id' => 'id']],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
            ['trote_id', 'validateTroteAtivo', 'skipOnError' => true],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote',
            'universidade_id' => 'Instituição',
            'nome' => 'Nome',
            'cargo' => 'Cargo ou função',
            'ordem' => 'Ordem de exibição',
            'ativo' => 'Publicado para participantes',
            'created_at' => 'Criado em',
            'updated_at' => 'Atualizado em',
        ];
    }

    public function getUniversidade()
    {
        return $this->hasOne(Universidade::class, ['id' => 'universidade_id']);
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function validateTroteAtivo($attribute): void
    {
        if (!$this->isNewRecord && !$this->isAttributeChanged($attribute)) {
            return;
        }

        $troteAtivoExiste = Trote::find()
            ->where(['id' => $this->$attribute, 'status' => Trote::STATUS_ATIVO])
            ->exists();

        if (!$troteAtivoExiste) {
            $this->addError($attribute, 'Selecione um trote ativo.');
        }
    }
}
