<?php

namespace app\modules\common\models;

use Yii;

class Trote extends \yii\db\ActiveRecord{

    public static function tableName(){
        return '_trote';
    }

   public function rules(){
        return [
            // obrigatórios
            [['nome', 'frase_certificado', 'ativo'], 'required'],

            // strings
            [['nome'], 'string', 'max' => 255],
            [['frase_certificado'], 'string', 'max' => 500],

            // status
            [['ativo'], 'in', 'range' => [0, 1]],
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
 