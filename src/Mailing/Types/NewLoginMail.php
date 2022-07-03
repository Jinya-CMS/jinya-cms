<?php

namespace App\Mailing\Types;

use App\Database\ApiKey;
use App\Mailing\Factory\MailerFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Jenssegers\Agent\Agent;
use JsonException;
use League\Plates\Engine;
use PHPMailer\PHPMailer\Exception;
use Throwable;

/**
 * This class is the new login mail and should be sent when a new login was registered
 */
class NewLoginMail
{
    /** @var Engine The template engine used for the email */
    private Engine $templateEngine;

    /**
     * NewLoginMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = \App\Theming\Engine::getPlatesEngine();
    }

    /**
     * Sends the new login mail
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param ApiKey $apiKey
     * @throws Exception
     * @throws GuzzleException
     * @throws JsonException
     * @throws Throwable
     */
    public function sendMail(string $artistEmail, string $artistName, ApiKey $apiKey): void
    {
        $client = new Client();
        $result = $client->get("http://ip-api.com/json/$apiKey->remoteAddress");
        $userAgent = new Agent(userAgent: $apiKey->userAgent);
        $browser = $userAgent->browser();
        $platform = $userAgent->platform();
        $location = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
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
        $mailer->setFrom(getenv('MAILER_FROM') ?: '');
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
