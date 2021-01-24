<?php

namespace App\Web\Actions\Authentication;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Mailing\Types\NewLoginMail;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\BadCredentialsException;
use App\Web\Exceptions\UnknownDeviceException;
use DateTime;
use Exception;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Throwable;

#[JinyaAction('/api/login', JinyaAction::POST)]
#[RequiredFields(['username', 'password'])]
class LoginAction extends Action
{
    public function __construct(LoggerInterface $logger, public Engine $engine)
    {
        parent::__construct($logger);
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';
        $twoFactorCode = $body['twoFactorCode'] ?? '';
        $knownDeviceCode = $this->request->getHeaderLine('JinyaDeviceCode');

        $artist = Artist::findByEmail($username);
        if (null !== $artist && $artist->validatePassword($password)) {
            if (!empty($knownDeviceCode) && $artist->validateDevice($knownDeviceCode)) {
                $knownDevice = KnownDevice::findByCode($knownDeviceCode);
            } elseif ($artist->twoFactorToken === $twoFactorCode) {
                $knownDevice = new KnownDevice();
                $knownDevice->setDeviceKey();
                $knownDevice->userId = (int)$artist->id;
                $knownDevice->create();
            } elseif (empty($knownDeviceCode) && empty($twoFactorCode)) {
                throw new UnknownDeviceException($this->request, 'Unknown device');
            } else {
                throw new BadCredentialsException($this->request, 'Bad credentials');
            }

            $apiKey = new ApiKey();
            $apiKey->userId = (int)$artist->id;
            $apiKey->setApiKey();
            $apiKey->validSince = new DateTime();
            $userAgentHeader = $this->request->getHeaderLine('User-Agent');

            if (!empty($userAgentHeader)) {
                $apiKey->userAgent = $userAgentHeader;
                $knownDevice->userAgent = $userAgentHeader;
            } else {
                $apiKey->userAgent = 'unknown';
                $knownDevice->userAgent = 'unknown';
            }

            $apiKey->remoteAddress = $_SERVER['REMOTE_ADDR'];
            $knownDevice->remoteAddress = $_SERVER['REMOTE_ADDR'];

            $knownDevice->update();
            $apiKey->create();

            $artist->twoFactorToken = null;
            $artist->update();

            try {
                $mail = new NewLoginMail($this->engine);
                $mail->sendMail($artist->email, $artist->artistName, $apiKey);
            } catch (Throwable $exception) {
                $this->logger->warning($exception->getMessage());
            }

            return $this->respond(
                [
                    'apiKey' => $apiKey->apiKey,
                    'deviceCode' => $knownDevice->deviceKey,
                    'roles' => $artist->roles,
                ]
            );
        }

        throw new BadCredentialsException($this->request, 'Bad credentials');
    }
}
