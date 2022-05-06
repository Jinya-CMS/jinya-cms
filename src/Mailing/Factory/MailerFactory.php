<?php

namespace App\Mailing\Factory;

use PHPMailer\PHPMailer\PHPMailer;

/**
 *
 */
class MailerFactory
{
    public static function getMailer(): PHPMailer
    {
        $mailer = new PHPMailer();
        $mailer->Host = getenv('MAILER_HOST');
        $mailer->SMTPAuth = getenv('MAILER_SMTP_AUTH');
        $mailer->Username = getenv('MAILER_USERNAME');
        $mailer->Password = getenv('MAILER_PASSWORD');
        $mailer->Port = getenv('MAILER_PORT') ?: 25;
        $mailer->SMTPSecure = getenv('MAILER_ENCRYPTION');
        $mailer->isSMTP();
        PHPMailer::$validator = 'html5';

        return $mailer;
    }
}
