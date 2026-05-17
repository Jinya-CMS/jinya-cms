<?php

namespace Jinya\Cms\Mailing\Types;

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use Jinya\Cms\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * This class is the two-factor mail and should be sent when a two-factor code was requested
 */
readonly class TwoFactorMail
{
    private PlatesEngine $templateEngine;
    private LoggerInterface $logger;

    /**
     * NewLoginMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = Engine::getPlatesEngine();
        $this->logger = Logger::getLogger();
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
        $this->logger->debug('Prepare two factor mail');
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
        /** @phpstan-ignore argument.type */
        $mailer->setFrom(JinyaConfiguration::getConfiguration()->get("from", "mailer"));
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $this->logger->debug('Send new two factor mail');
        $mailer->send();
    }
}
