<?php

namespace app\modules\common\models;

use Yii;

/**
 * This is the model class for table "_politica_privacidade".
 *
 * @property int $id
 * @property string|null $version
 * @property string|null $text
 * @property int|null $ativo
 * @property int|null $vigencia
 * @property string|null $data_create
 * @property string|null $data_update
 * @property int|null $user_create
 * @property int|null $user_update
 *
 * @property Users $userCreate
 * @property Users $userUpdate
 */
class PoliticaPrivacidade extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'simers_site_wp._politica_privacidade';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['ativo', 'vigencia', 'user_create', 'user_update'], 'integer'],
            [['data_create', 'data_update'], 'safe'],
            [['version'], 'string', 'max' => 45],
            [['user_create'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_create' => 'id']],
            [['user_update'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_update' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => 'Version',
            'text' => 'Text',
            'ativo' => 'Ativo',
            'vigencia' => 'Vigencia',
            'data_create' => 'Data Create',
            'data_update' => 'Data Update',
            'user_create' => 'User Create',
            'user_update' => 'User Update',
        ];
    }

    /**
     * Gets query for [[UserCreate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreate()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_create']);
    }

    /**
     * Gets query for [[UserUpdate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserUpdate()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_update']);
    }
}
