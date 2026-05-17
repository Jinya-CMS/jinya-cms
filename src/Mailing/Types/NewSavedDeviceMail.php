<?php

namespace Jinya\Cms\Mailing\Types;

use Asika\Agent\Agent;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\KnownDevice;
use Jinya\Cms\Locate\IpToLocationService;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use Jinya\Cms\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * This class is the new saved device mail and should be sent when a new saved device was registered
 */
readonly class NewSavedDeviceMail
{
    private PlatesEngine $templateEngine;
    private LoggerInterface $logger;

    /**
     * NewSavedDeviceMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = Engine::getPlatesEngine();
        $this->logger = Logger::getLogger();
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
        $this->logger->debug('Prepare new saved device mail');
        $userAgent = new Agent(userAgent: $knownDevice->userAgent);
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = new IpToLocationService()->locateIp($knownDevice->remoteAddress);
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
        $mailer->Subject = 'New-saved device for your account';
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
