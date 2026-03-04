<?php

namespace app\modules\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;


class Documento extends ActiveRecord
{
    public $file;

    const TIPO_INFORMATIVO = 'informativo';
    const TIPO_REGULAMENTO = 'regulamento';

    public static function tableName()
    {
        return '{{%documentos}}';
    }

    public function rules()
    {
        return [
            [['nome', 'tipo'], 'required'], // remove 'arquivo'
            [['nome', 'arquivo'], 'string', 'max' => 255],
            [['tipo'], 'in', 'range' => [
                self::TIPO_INFORMATIVO,
                self::TIPO_REGULAMENTO
            ]],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
            'tipo' => 'Tipo',
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

    /**
     * Lista de tipos para dropdown
     */
    public static function getTipos()
    {
        return [
            self::TIPO_INFORMATIVO => 'Informativo',
            self::TIPO_REGULAMENTO => 'Regulamento',
        ];
    }
}
