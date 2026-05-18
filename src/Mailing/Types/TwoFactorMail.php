<?php

namespace Jinya\Cms\Mailing\Types;

use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the two-factor mail and should be sent when a two-factor code was requested
 */
readonly class TwoFactorMail extends BaseMail
{
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
        $this->logger->debug('Prepare two-factor mail');
        $subject = $this->translate('two_factor_code_subject');
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::TwoFactorCodeHtml',
            [
                'artistName' => $artistName,
                'twoFactorCode' => $twoFactorCode,
                'subject' => $subject,
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
        $mailer->Subject = $subject;
        /** @phpstan-ignore argument.type */
        $mailer->setFrom(JinyaConfiguration::getConfiguration()->get("from", "mailer"));
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $this->logger->debug('Send new two-factor mail');
        $mailer->send();
    }
}
