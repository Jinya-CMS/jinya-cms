<?php

namespace App\Mailing\Types;

use App\Database\KnownDevice;
use App\Mailing\Factory\MailerFactory;
use Jenssegers\Agent\Agent;
use JsonException;
use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the new saved device mail and should be sent when a new saved device was registered
 */
class NewSavedDeviceMail
{
    /** @var Engine The template engine used for the email */
    private Engine $templateEngine;

    /**
     * NewSavedDeviceMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = \App\Theming\Engine::getPlatesEngine();
    }

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
        $userAgent = new Agent(userAgent: $knownDevice->userAgent ?? '');
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = json_decode(file_get_contents("https://ip.jinya.de/?ip=$knownDevice->remoteAddress"), true, 512, JSON_THROW_ON_ERROR);
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::NewSavedDeviceHtml',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $knownDevice->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
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
        $mailer->Subject = 'New saved device for your account';
        $mailer->setFrom(getenv('MAILER_FROM') ?: '');
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
