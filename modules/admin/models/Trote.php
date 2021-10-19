<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "_trote".
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $frase_certificado
 * @property int|null $ativo
 */
class Trote extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_trote';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'frase_certificado'], 'string'],
            [['ativo'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'frase_certificado' => 'Frase Certificado',
            'ativo' => 'Ativo',
        ];
    }
}
