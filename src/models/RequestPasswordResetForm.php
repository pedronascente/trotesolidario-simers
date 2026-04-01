<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

class RequestPasswordResetForm extends Model
{
    public $email;

    private $_user;

    public function rules()
    {
        return [
            ['email', 'required'],
            ['email', 'trim'],
            ['email', 'email'],
            ['email', 'validateEmail'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'E-mail',
        ];
    }

    public function validateEmail($attribute)
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->getUser() === null) {
            $this->addError($attribute, 'Não existe usuário ativo com este e-mail.');
        }
    }

    public function sendEmail()
    {
        if (!$this->validate()) {
            return false;
        }

        $user = $this->getUser();
        if ($user === null) {
            return false;
        }

        try {
            if (!User::isPasswordResetTokenValid($user->password_reset_token)) {
                $user->generatePasswordResetToken();
            }

            $user->updated_at = date('Y-m-d H:i:s');
            if (!$user->save(false, ['password_reset_token', 'updated_at'])) {
                $this->addError('email', 'Não foi possível preparar o link de recuperação agora.');
                return false;
            }

            $resetLink = Url::to(['/auth/reset-password', 'token' => $user->password_reset_token], true);
            $html = $this->buildHtmlMessage($user->nome, $resetLink);
            $sender = $this->resolveSender();

            $mail = Yii::$app->mailer
                ->compose('layouts/html', ['content' => $html])
                ->setTo($user->email)
                ->setFrom($sender)
                ->setSubject('Redefinição de senha - Trote Solidário')
                ->setTextBody(
                    "Olá, {$user->nome}!\n\n"
                    . "Recebemos uma solicitação para redefinir sua senha.\n"
                    . "Acesse o link abaixo para cadastrar uma nova senha:\n\n"
                    . $resetLink . "\n\n"
                    . "Se você não solicitou essa alteração, ignore este e-mail."
                );

            $bcc = Yii::$app->params['recoveryBccEmail'] ?? 'desenvolvimento@simers.org.br';
            if (!empty($bcc)) {
                $mail->setBcc($bcc);
            }

            $sent = $mail->send();

            if (!$sent) {
                $this->addError('email', 'Não foi possível enviar o e-mail de recuperação no momento.');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Yii::error('Falha ao solicitar recuperação de senha: ' . $e->getMessage(), __METHOD__);
            $this->addError('email', 'Não foi possível processar a recuperação de senha no momento.');
            return false;
        }
    }

    private function getUser()
    {
        if ($this->_user === null && !empty($this->email)) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }

    private function buildHtmlMessage($nome, $resetLink)
    {
        $nomeSeguro = htmlspecialchars((string) $nome, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $linkSeguro = htmlspecialchars($resetLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">'
            . '<p>Olá, <strong>' . $nomeSeguro . '</strong>!</p>'
            . '<p>Recebemos uma solicitação para redefinir sua senha.</p>'
            . '<p><a href="' . $linkSeguro . '">Clique aqui para cadastrar uma nova senha</a></p>'
            . '<p>Se você não solicitou essa alteração, ignore este e-mail.</p>'
            . '<hr style="border:0;border-top:1px solid #ddd;margin:24px 0;">'
            . '<p style="font-size:12px;color:#666;margin:0;">Trote Solidário Simers</p>'
            . '</div>';
    }

    private function resolveSender()
    {
        $transport = Yii::$app->mailer->transport;
        $smtpUser = null;

        if (is_object($transport) && method_exists($transport, 'getUsername')) {
            $smtpUser = $transport->getUsername();
        } elseif (is_object($transport) && property_exists($transport, 'username')) {
            $smtpUser = $transport->username;
        }

        $email = $smtpUser ?: (Yii::$app->params['senderEmail'] ?? null);
        $name = Yii::$app->params['senderName'] ?? 'Trote Solidário';

        if (empty($email)) {
            return null;
        }

        return [$email => $name];
    }
}