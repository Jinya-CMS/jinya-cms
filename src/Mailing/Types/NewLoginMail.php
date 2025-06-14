<?php

namespace Jinya\Cms\Mailing\Types;

use Asika\Agent\Agent;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Mailing\Factory\MailerFactory;
use Jinya\Cms\Theming\Engine;
use Jinya\Plates\Engine as PlatesEngine;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the new login mail and should be sent when a new login was registered
 */
readonly class NewLoginMail
{
    private PlatesEngine $templateEngine;

    /**
     * NewLoginMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = Engine::getPlatesEngine();
    }

    /**
     * Sends the new login mail
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param ApiKey $apiKey
     * @throws Exception
     * @throws JsonException
     * @throws Throwable
     */
    public function sendMail(string $artistEmail, string $artistName, ApiKey $apiKey): void
    {
        $userAgent = new Agent(userAgent: $apiKey->userAgent ?? '');
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = json_decode(
            file_get_contents("https://ip.jinya.de/?ip=$apiKey->remoteAddress") ?: '{}',
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        $renderedHtmlMail = $this->templateEngine->render(
            'mailing::NewLoginHtml',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $apiKey->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
            ],
        );
        $renderedTextMail = $this->templateEngine->render(
            'mailing::NewLoginText',
            [
                'artistName' => $artistName,
                'location' => $location,
                'remoteAddress' => $apiKey->remoteAddress,
                'platform' => $platform,
                'browser' => $browser,
            ],
        );

        $mailer = MailerFactory::getMailer();
        $mailer->Subject = 'New login for your account';
        /** @phpstan-ignore-next-line */
        $mailer->setFrom(JinyaConfiguration::getConfiguration()->get("from", "mailer"));
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
