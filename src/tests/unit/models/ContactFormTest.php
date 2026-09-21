<?php

namespace tests\unit\models;

require_once dirname(__DIR__, 3) . '/modules/common/models/ContactForm.php';

use app\modules\common\models\ContactForm;
use yii\mail\MessageInterface;
use yii\mail\BaseMailer;
use yii\mail\MailEvent;

class ContactFormTest extends \Codeception\Test\Unit
{
    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;

    public function testEmailIsSentOnContact()
    {
        $emailMessage = null;
        $afterSend = static function (MailEvent $event) use (&$emailMessage): void {
            if ($event->isSuccessful) {
                $emailMessage = $event->message;
            }
        };
        \Yii::$app->mailer->on(BaseMailer::EVENT_AFTER_SEND, $afterSend);

        /** @var ContactForm $model */
        $this->model = $this->getMockBuilder(ContactForm::class)
            ->setMethods(['validate'])
            ->getMock();

        $this->model->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $this->model->attributes = [
            'name' => 'Tester',
            'email' => 'tester@example.com',
            'subject' => 'very important letter subject',
            'body' => 'body of current message',
        ];

        $this->assertTrue($this->model->contact('admin@example.com'));
        \Yii::$app->mailer->off(BaseMailer::EVENT_AFTER_SEND, $afterSend);

        $this->assertInstanceOf(MessageInterface::class, $emailMessage);
        $this->assertArrayHasKey('admin@example.com', $emailMessage->getTo());
        $this->assertArrayHasKey('noreply@example.com', $emailMessage->getFrom());
        $this->assertArrayHasKey('tester@example.com', $emailMessage->getReplyTo());
        $this->assertSame('very important letter subject', $emailMessage->getSubject());
        $this->assertStringContainsString('body of current message', $emailMessage->toString());
    }
}
