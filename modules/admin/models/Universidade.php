<?php

namespace app\modules\admin\models;

use Yii;

/**
 * This is the model class for table "_universidade".
 *
 * @property int $id
 * @property int|null $trote_id
 * @property string|null $nome
 * @property string|null $icon
 * @property string|null $link_doacao_alimento
 * @property int|null $ativo
 *
 * @property Trote $trote
 */
class Universidade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '_universidade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['trote_id', 'ativo'], 'integer'],
            [['nome', 'icon', 'link_doacao_alimento'], 'string'],
            [['trote_id'], 'exist', 'skipOnError' => true, 'targetClass' => Trote::className(), 'targetAttribute' => ['trote_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'trote_id' => 'Trote ID',
            'nome' => 'Nome',
            'icon' => 'Icon',
            'link_doacao_alimento' => 'Link Doacao Alimento',
            'ativo' => 'Ativo',
        ];
    }

    /**
     * Gets query for [[Trote]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrote()
    {
        return $this->hasOne(Trote::className(), ['id' => 'trote_id']);
    }
}
