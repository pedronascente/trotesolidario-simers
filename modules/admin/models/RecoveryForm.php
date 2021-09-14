<?php

namespace app\modules\admin\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class RecoveryForm extends Model {

    public $email;

    /**
     * {@inheritdoc}
     */
    public function rules() {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'email' => Yii::t('app', 'Email'),
        ];
    }

    public function sendEmail() {
        /* @var $user User */
        $user = Users::findOne([
                    'status' => Users::STATUS_ACTIVE,
                    'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }

        if (!Users::isPasswordResetTokenValid($user->passwordResetToken)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }
        
        $html = "<p>Acesse o link abaixo para trocar sua senha</p><br>"; 
        $html .= "<a href='".Url::base(true)."/admin/new-password?token=$user->passwordResetToken'>Clique aqui para criar uma nova senha</a>"; 

        return Yii::$app->mailer->compose('layouts/html', ['content' => $html])
                        ->setFrom('noreply@simers.org.br')
                        ->setTo($this->email)
                        ->setSubject('Password reset for ' . Yii::$app->name)
                        ->send();
    }

}
