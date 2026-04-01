<?php

namespace app\modules\common\models;

use yii\base\Model;

/**
 * ChangePasswordForm form
 */
class ChangePasswordForm extends Model
{
    public $userId;
    public $new_password;
    public $confirm_password;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['new_password', 'confirm_password'], 'required'],
            [['new_password'], 'string', 'min' => 6],
            ['confirm_password', 'compare', 'compareAttribute' => 'new_password', 'message' => 'As senhas não conferem.'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'new_password' => 'Nova Senha',
            'confirm_password' => 'Confirmar Senha',
        ];
    }
}

