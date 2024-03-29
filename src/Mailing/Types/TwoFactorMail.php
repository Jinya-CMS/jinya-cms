<?php

namespace App\Mailing\Types;

use App\Mailing\Factory\MailerFactory;
use App\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the two-factor mail and should be sent when a two-factor code was requested
 */
readonly class TwoFactorMail
{
    private PlatesEngine $templateEngine;

    /**
     * TwoFactorMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = Engine::getPlatesEngine();
    }

    /**
     * Sends the two-factor email
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param string $twoFactorCode
     * @throws Exception
     * @throws Throwable
     */
    public function sendMail(string $artistEmail, string $artistName, string $twoFactorCode): void
    {
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::TwoFactorCodeHtml',
            [
                'artistName' => $artistName,
                'twoFactorCode' => $twoFactorCode,
            ],
        );
        $renderedTextMail = $this->templateEngine->render(
            'mailing::TwoFactorCodeText',
            [
                'artistName' => $artistName,
                'twoFactorCode' => $twoFactorCode,
            ],
        );

        $mailer = MailerFactory::getMailer();
        $mailer->Subject = 'Your two-factor code';
        $mailer->setFrom(getenv('MAILER_FROM') ?: '');
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
