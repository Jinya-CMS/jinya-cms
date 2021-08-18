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

class NewLoginMail
{
    private Engine $templateEngine;

    /**
     * TwoFactorMail constructor.
     */
    public function __construct()
    {
        $this->templateEngine = \App\Theming\Engine::getPlatesEngine();
    }

    /**
     * Sends the two factor email
     *
     * @param string $artistEmail
     * @param string $artistName
     * @param ApiKey $apiKey
     * @throws Exception
     * @throws JsonException
     * @throws GuzzleException
     */
    public function sendMail(string $artistEmail, string $artistName, ApiKey $apiKey): void
    {
        $client = new Client();
        $result = $client->get("https://freegeoip.app/json/$apiKey->remoteAddress");
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
        $mailer->setFrom(getenv('MAILER_FROM'));
        $mailer->addAddress($artistEmail);
        $mailer->AltBody = $renderedTextMail;
        $mailer->Body = $renderedHtmlMail;
        $mailer->isHTML();

        $mailer->send();
    }
}
