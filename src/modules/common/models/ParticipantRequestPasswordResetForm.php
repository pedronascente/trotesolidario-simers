<?php

namespace app\modules\common\models;

use app\models\User;
use Yii;
use yii\base\Model;
use yii\helpers\Url;

class ParticipantRequestPasswordResetForm extends Model
{
    public $email;
    public $cpf;

    private ?User $_user = null;

    public function rules(): array
    {
        return [
            [['email', 'cpf'], 'required'],
            [['email'], 'trim'],
            [['cpf'], 'filter', 'filter' => fn($value) => preg_replace('/\D/', '', (string) $value)],
            [['email'], 'email'],
            [['cpf'], 'validateCpf'],
            [['email'], 'validateUserMatch'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'email' => 'E-mail',
            'cpf' => 'CPF',
        ];
    }

    public function sendEmail(): bool
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
            if (!$this->saveResetToken($user)) {
                $this->addError('email', 'Nao foi possivel preparar o link de recuperacao agora.');
                return false;
            }

            $resetLink = Url::to(['/participante/new-password/index', 'token' => $user->password_reset_token], true);
            if (!$this->sendResetEmail($user, $resetLink)) {
                $this->addError('email', 'Nao foi possivel enviar o e-mail de recuperacao no momento.');
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Yii::error('Falha ao solicitar recuperacao de senha do participante: ' . $e->getMessage(), __METHOD__);
            $this->addError('email', 'Nao foi possivel processar a recuperacao de senha no momento.');
            return false;
        }
    }

    public function validateCpf($attribute): void
    {
        $user = new User();
        $user->$attribute = $this->$attribute;
        $user->validateCpf($attribute);

        foreach ($user->getErrors($attribute) as $error) {
            $this->addError($attribute, $error);
        }
    }

    public function validateUserMatch($attribute): void
    {
        if ($this->hasErrors()) {
            return;
        }

        if ($this->getUser() === null) {
            $this->addError($attribute, 'Nao encontramos um usuario ativo com o e-mail e CPF informados.');
        }
    }

    protected function findUserByEmailAndCpf(string $email, string $cpf): ?User
    {
        return User::find()
            ->where(['email' => $email, 'cpf' => $cpf, 'status' => User::STATUS_ACTIVE])
            ->one();
    }

    protected function saveResetToken(User $user): bool
    {
        return $user->save(false, ['password_reset_token', 'updated_at']);
    }

    protected function sendResetEmail(User $user, string $resetLink): bool
    {
        $html = $this->buildHtmlMessage($user->nome, $resetLink);
        $sender = $this->resolveSender();

        $mail = Yii::$app->mailer
            ->compose('layouts/html', ['content' => $html])
            ->setTo($user->email)
            ->setFrom($sender)
            ->setSubject('Redefinicao de senha - Trote Solidario')
            ->setTextBody(
                "Ola, {$user->nome}!\n\n"
                . "Recebemos uma solicitacao para redefinir sua senha.\n"
                . "Acesse o link abaixo para cadastrar uma nova senha:\n\n"
                . $resetLink . "\n\n"
                . "Se voce nao solicitou essa alteracao, ignore este e-mail."
            );

        $bcc = Yii::$app->params['recoveryBccEmail'] ?? 'desenvolvimento@simers.org.br';
        if (!empty($bcc)) {
            $mail->setBcc($bcc);
        }

        return $mail->send();
    }

    private function getUser(): ?User
    {
        if ($this->_user === null && !$this->hasErrors()) {
            $this->_user = $this->findUserByEmailAndCpf((string) $this->email, (string) $this->cpf);
        }

        return $this->_user;
    }

    private function buildHtmlMessage(string $nome, string $resetLink): string
    {
        $nomeSeguro = htmlspecialchars($nome, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $linkSeguro = htmlspecialchars($resetLink, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return '<div style="font-family: Arial, sans-serif; color: #333; line-height: 1.6;">'
            . '<p>Ola, <strong>' . $nomeSeguro . '</strong>!</p>'
            . '<p>Recebemos uma solicitacao para redefinir sua senha.</p>'
            . '<p><a href="' . $linkSeguro . '">Clique aqui para cadastrar uma nova senha</a></p>'
            . '<p>Se voce nao solicitou essa alteracao, ignore este e-mail.</p>'
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
        $name = Yii::$app->params['senderName'] ?? 'Trote Solidario';

        return empty($email) ? null : [$email => $name];
    }
}
