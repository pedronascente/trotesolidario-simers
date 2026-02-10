<?php

namespace app\modules\common\models;

use Yii;

class Informativo extends \yii\db\ActiveRecord{
    public $file;
    
    public static function tableName(){
        return '_informativo';
    }

    public function rules(){
        return [
            [['nome', 'arquivo'], 'string'],
            [['nome'], 'required'],
        ];
    }

    public function attributeLabels(){
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'arquivo' => 'Arquivo',
        ];
    }
}
 