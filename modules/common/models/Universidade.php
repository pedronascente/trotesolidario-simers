<?php

namespace app\modules\common\models;

use Yii;

class Universidade extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName(){
        return '_universidade';
    }
 
    public function rules(){
        return [
            [['nome', 'trote_id', 'ativo', 'link_doacao_alimento'], 'required'],
            [['trote_id', 'ativo'], 'integer'],
            [['nome'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 255],
            [['link_doacao_alimento'], 'string', 'max' => 500],
            ['link_doacao_alimento', 'url', 'defaultScheme' => 'https'],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::class, 'targetAttribute' => ['trote_id' => 'id']],
            [
                'file',
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'maxSize' => 1024 * 1024 * 2,
                'mimeTypes' => 'image/*',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Evento',
            'nome' => 'Nome da Universidade',
            'icon' => 'Imagem',
            'file' => 'Imagem',
            'link_doacao_alimento' => 'Link para Doação de Alimentos',
            'ativo' => 'Status',
        ];
    }

    public function getTrote()
    {
        return $this->hasOne(Trote::class, ['id' => 'trote_id']);
    }

    public function getUsers()
    {
        return $this->hasMany(Users::class, ['instituicao' => 'id']);
    }
}
