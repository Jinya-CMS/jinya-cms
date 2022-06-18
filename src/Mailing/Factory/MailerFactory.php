<?php

namespace App\Mailing\Factory;

use PHPMailer\PHPMailer\PHPMailer;

/**
 * A simple factory class to create a new PHPMailer instance
 * @see \PHPMailer\PHPMailer\PHPMailer
 */
abstract class MailerFactory
{
    /**
     * Creates a new PHPMailer instance based on the environment variables configured
     *
     * @return PHPMailer
     */
    public static function getMailer(): PHPMailer
    {
        $mailer = new PHPMailer();
        $mailer->Host = getenv('MAILER_HOST') ?: '';
        $smtpAuth = getenv('MAILER_SMTP_AUTH');
        $mailer->SMTPAuth = is_string($smtpAuth) && strtolower($smtpAuth) === 'true';
        $mailer->Username = getenv('MAILER_USERNAME') ?: '';
        $mailer->Password = getenv('MAILER_PASSWORD') ?: '';
        $mailer->Port = (int)getenv('MAILER_PORT') ?: 25;
        $mailer->SMTPSecure = getenv('MAILER_ENCRYPTION') ?: '';
        $mailer->isSMTP();
        PHPMailer::$validator = 'html5';

        return $mailer;
    }
}
