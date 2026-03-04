<?php

namespace app\modules\common\models;

use Yii;

class Regulamento extends \yii\db\ActiveRecord{
    public $file;

    public static function tableName(){
        return '_regulamento';
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
