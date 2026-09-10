<?php

namespace app\modules\common\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;

class AlbumFoto extends ActiveRecord
{
    /** @var UploadedFile|null */
    public $arquivoImagem;

    public static function tableName()
    {
        return '{{%album_foto}}';
    }

    public function behaviors()
    {
        return [[
            'class' => TimestampBehavior::class,
            'createdAtAttribute' => 'created_at',
            'updatedAtAttribute' => 'updated_at',
            'value' => new Expression('NOW()'),
        ]];
    }

    public function rules()
    {
        return [
            [['participacao_id', 'titulo'], 'required'],
            [['participacao_id'], 'integer'],
            [['titulo'], 'trim'],
            [['titulo'], 'string', 'max' => 160],
            [['imagem'], 'string', 'max' => 255],
            [['participacao_id'], 'exist', 'skipOnError' => true, 'targetClass' => Participacao::class, 'targetAttribute' => ['participacao_id' => 'id']],
            [['arquivoImagem'], 'image',
                'extensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp'],
                'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                'maxSize' => 10 * 1024 * 1024,
                'skipOnEmpty' => !$this->isNewRecord,
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participacao_id' => 'Participação no trote',
            'titulo' => 'Título',
            'imagem' => 'Imagem',
            'arquivoImagem' => 'Imagem',
            'created_at' => 'Criada em',
            'updated_at' => 'Atualizada em',
        ];
    }

    public function getParticipacao()
    {
        return $this->hasOne(Participacao::class, ['id' => 'participacao_id']);
    }

    public function getImagemPath(): string
    {
        return \Yii::getAlias('@app/web/imagens/album-fotos') . DIRECTORY_SEPARATOR . $this->imagem;
    }
}
