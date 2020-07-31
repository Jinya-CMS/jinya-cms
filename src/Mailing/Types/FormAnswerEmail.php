<?php

namespace App\Mailing\Types;

use App\Mailing\Factory\MailerFactory;
use PHPMailer\PHPMailer\Exception;

class FormAnswerEmail
{

    /**
     * Sends an answer to a message
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param string $toAddress
     * @param string $subject
     * @param string $answer
     * @throws Exception
     */
    public function sendMail(
        string $artistEmail,
        string $artistName,
        string $toAddress,
        string $subject,
        string $answer
    ): void {
        $mailer = MailerFactory::getMailer();
        $mailer->setFrom(getenv('MAILER_FROM'), $artistName);
        $mailer->addAddress($toAddress);
        $mailer->addBCC($artistEmail, $artistName);
        $mailer->Subject = $subject;
        $mailer->addReplyTo($artistEmail, $artistName);
        $mailer->Body = $answer;
        $mailer->isHTML();

        $mailer->send();
    }
}