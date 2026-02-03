<?php

namespace app\modules\common\models;

use Yii;

class Trote extends \yii\db\ActiveRecord{

    public static function tableName(){
        return '_trote';
    }

    public function rules()
    {
        return [
            [
                ['nome', 'frase_certificado'], 
                'string'
            ],
            [
                ['nome', 'frase_certificado'], 
                'required'
            ],
            [
                ['ativo'], 
                'integer'
            ],
        ];
    }

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
 