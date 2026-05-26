<?php

namespace Jinya\Cms\Mailing\Types;

use Asika\Agent\Agent;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\KnownDevice;
use Jinya\Cms\Locate\IpToLocationService;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the new saved device mail and should be sent when a new saved device was registered
 */
readonly class NewSavedDeviceMail extends BaseMail
{
    /**
     * Sends the new saved device email
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param KnownDevice $knownDevice
     *
     * @throws Exception
     * @throws JsonException
     * @throws Throwable
     */
    public function sendMail(string $artistEmail, string $artistName, KnownDevice $knownDevice): void
    {
        $this->logger->debug('Prepare new saved device mail');
        $userAgent = new Agent(userAgent: $knownDevice->userAgent);
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = new IpToLocationService()->locateIp($knownDevice->remoteAddress);
        $subject = $this->translate('new_device_subject');
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::NewSavedDeviceHtml',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $knownDevice->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
                'subject' => $subject,
            ],
        );
        $renderedTextMail = $this->templateEngine->render(
            'mailing::NewSavedDeviceText',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $knownDevice->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
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

        $this->logger->debug('Send new known device mail');
        $mailer->send();
    }
}
