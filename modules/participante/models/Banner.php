<?php

namespace app\modules\participante\models;

use Yii;

/**
 * This is the model class for table "_banner".
 *
 * @property int $id
 * @property string|null $posicao
 * @property string|null $img_mob
 * @property string|null $img_dsk
 */
class Banner extends \yii\db\ActiveRecord
{
    public $file_dsk;
    public $file_mob;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_banner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['posicao', 'img_mob', 'img_dsk'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'posicao' => 'Posicao',
            'img_mob' => 'Img Mob',
            'img_dsk' => 'Img Dsk',
        ];
    }
}
