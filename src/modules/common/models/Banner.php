<?php

namespace app\modules\common\models;

use yii\db\ActiveRecord;

class Banner extends ActiveRecord{

    public const TIPO_LOGIN       = 'Login';
    public const TIPO_HOME        = 'Home';

    public $file_dsk;
    public $file_mob;

    public static function tableName(){
        return 'banner';
    }

    public function rules(){
        return [
            [['tipo','ativo'], 'required'],
            [['ativo'], 'integer'],
            ['ativo', 'in', 'range' => [0, 1]],
            ['tipo', 'in', 'range' => array_keys(self::getLocaisExibicao()), 'message' => 'Selecione um local de exibição válido para o banner.'],
            ['tipo', 'unique', 'message' => 'Já existe um banner cadastrado para este local de exibição.'],
            [
                ['file_dsk', 'file_mob'],
                'image',
                'extensions' => ['jpg', 'jpeg', 'png'],
                'mimeTypes' => ['image/jpeg', 'image/png'],
                'maxSize' => 5 * 1024 * 1024,
                'skipOnEmpty' => true,
            ],
            [['file_dsk'], 'validateImagePresence', 'skipOnEmpty' => false],
            [['img_dsk', 'img_mob'], 'string', 'max' => 255],
        ];
    }

    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['tipo', 'ativo', 'file_dsk', 'file_mob'],
        ];
    }

    public function validateImagePresence(string $attribute): void
    {
        $hasUpload = $this->file_dsk !== null || $this->file_mob !== null;
        $hasStoredImage = !$this->isNewRecord && (!empty($this->img_dsk) || !empty($this->img_mob));

        if (!$hasUpload && !$hasStoredImage) {
            $this->addError($attribute, 'Envie ao menos uma imagem para o banner.');
        }
    }

    public function attributeLabels(){
        return [
            'tipo'  => 'Local de exibição',
            'file_dsk' => 'Imagem Desktop',
            'file_mob' => 'Imagem Mobile',
            'ativo' => 'Ativo',
        ];
    }

    public static function getLocaisExibicao(): array{
        return [
            self::TIPO_LOGIN => 'Página de acesso dos participantes — antes do login',
            self::TIPO_HOME => 'Painel inicial do participante — após o login',
        ];
    }

    /**
     * @deprecated Use getLocaisExibicao().
     */
    public static function getPosicoes(): array{
        return self::getLocaisExibicao();
    }

    public function afterFind(){
        parent::afterFind();
        $this->ativo = (int) $this->ativo;
    }
}
