<?php

namespace app\modules\common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Universidade extends \yii\db\ActiveRecord
{
    public $file;

    public static function tableName(){
        return 'universidade';
    }

    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(){
        return [
            [['nome', 'ativo', 'cidade', 'uf', 'link_doacao_alimento'], 'required'],
            [['ativo'], 'integer'],
            [['nome','cidade'], 'string', 'max' => 255],
            [['uf'], 'string', 'max' => 2],
            [['icon'], 'string', 'max' => 255],
            [['link_doacao_alimento'], 'string', 'max' => 500],
            ['link_doacao_alimento', 'url', 'defaultScheme' => 'https'],
            [
                ['file'],
                'file',
                'skipOnEmpty' => true,
                'extensions' => ['jpg', 'jpeg', 'png', 'gif'],
                'maxSize' => 1024 * 1024 * 2,
                'mimeTypes' => 'image/*',
            ],
        ];
    }

    public function attributeLabels(){
        return [
            'id' => 'ID',
            'nome' => 'Universidade',
            'cidade' => 'Cidade',
            'uf' => 'Estado',
            'icon' => 'Imagem',
            'file' => 'Imagem',
            'link_doacao_alimento' => 'Link para Doação de Alimentos',
            'ativo' => 'Status',
        ];
    }

    public function getUsers(){
        return $this->hasMany(Users::class, ['instituicao' => 'id']);
    }

    public function getMercadosParceiros()
    {
        return $this->hasMany(MercadoParceiro::class, ['id' => 'mercado_id'])
            ->viaTable('{{%mercado_universidade}}', ['universidade_id' => 'id']);
    }
}
