<?php

namespace app\modules\common\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class Banner extends ActiveRecord{

    public const POSICAO_LOGIN       = 'Login';
    public const POSICAO_INFORMATIVO = 'Informativo';
    public const POSICAO_HOME        = 'Home';

    public $file_dsk;
    public $file_mob;

    public static function tableName(){
        return '_banner';
    }

    public function rules(){
        return [
            [['posicao','ativo'], 'required'],
            [['ativo'], 'integer'],
            [
                ['file_dsk', 'file_mob'],
                'file',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'skipOnEmpty' => true, 
            ],

            [['img_dsk', 'img_mob'], 'string'],
        ];
    }

    public function attributeLabels(){
        return [
            'posicao'  => 'Posição do Banner',
            'file_dsk' => 'Imagem Desktop',
            'file_mob' => 'Imagem Mobile',
            'ativo' => 'Ativo',
        ];
    }

    public static function getPosicoes(): array{
        return [
            self::POSICAO_LOGIN => 'Imagem da pagina de login dos Participantes',
            self::POSICAO_INFORMATIVO => 'Informativo',
            self::POSICAO_HOME => 'Home',
        ];
    }

    public function afterFind(){
        parent::afterFind();
        $this->ativo = (int) $this->ativo;
    }
}
