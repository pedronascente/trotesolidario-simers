<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "_informativo".
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $arquivo
 */
class Informativo extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_informativo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'arquivo'], 'string'],
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
            'arquivo' => 'Arquivo',
        ];
    }
}
