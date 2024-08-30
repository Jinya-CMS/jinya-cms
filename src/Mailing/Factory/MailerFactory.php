<?php

namespace Jinya\Cms\Mailing\Factory;

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Logging\Logger;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * A simple factory class to create a new PHPMailer instance
 * @see PHPMailer
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
        $configuration = JinyaConfiguration::getConfiguration();
        $mailer = new PHPMailer();
        $mailer->Host = (string)$configuration->get('host', 'mailer', '');
        $mailer->Username = (string)$configuration->get('username', 'mailer', '');
        $mailer->Password = (string)$configuration->get('password', 'mailer', '');
        $mailer->Port = (int)$configuration->get('port', 'mailer', 25);
        $mailer->SMTPSecure = (string)$configuration->get('encryption', 'mailer', '');
        $mailer->Debugoutput = Logger::getLogger();
        $mailer->SMTPAuth = (bool)$configuration->get('smtp_auth', 'mailer', false);
        $mailer->isSMTP();
        PHPMailer::$validator = 'html5';

        return $mailer;
    }
}
