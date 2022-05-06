<?php

namespace App\Web\Actions\Authentication;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Mailing\Types\NewLoginMail;
use App\Mailing\Types\NewSavedDeviceMail;
use App\Web\Actions\Action;
use App\Web\Exceptions\BadCredentialsException;
use App\Web\Exceptions\UnknownDeviceException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

/**
 *
 */
class LoginAction extends Action
{
    public NewLoginMail $newLoginMail;
    public NewSavedDeviceMail $newSavedDeviceMail;

    public function __construct()
    {
        parent::__construct();
        $this->newLoginMail = new NewLoginMail();
        $this->newSavedDeviceMail = new NewSavedDeviceMail();
    }

    /**
     * {@inheritDoc}
     * @throws Exception
     */
    protected function action(): Response
    {
        $username = $this->body['username'] ?? '';
        $password = $this->body['password'] ?? '';
        $twoFactorCode = $this->body['twoFactorCode'] ?? '';
        $knownDeviceCode = $this->request->getHeaderLine('JinyaDeviceCode');

        $artist = Artist::findByEmail($username);
        if ($artist !== null && $artist->validatePassword($password)) {
            $userAgentHeader = $this->request->getHeaderLine('User-Agent');
            if (!empty($knownDeviceCode) && $artist->validateDevice($knownDeviceCode)) {
                $knownDevice = KnownDevice::findByCode($knownDeviceCode);
            } elseif ($artist->twoFactorToken === $twoFactorCode) {
                $knownDevice = new KnownDevice();
                $knownDevice->setDeviceKey();
                $knownDevice->userId = (int)$artist->id;
                $knownDevice->remoteAddress = $this->request->getHeaderLine(
                    'X-Forwarded-For'
                ) ?: $_SERVER['REMOTE_ADDR'];
                if (!empty($userAgentHeader)) {
                    $knownDevice->userAgent = $userAgentHeader;
                } else {
                    $knownDevice->userAgent = 'unknown';
                }
                $knownDevice->create();
                try {
                    $this->newSavedDeviceMail->sendMail($artist->email, $artist->artistName, $knownDevice);
                } catch (Throwable $exception) {
                    $this->logger->warning($exception->getMessage());
                }
            } elseif (empty($knownDeviceCode) && empty($twoFactorCode)) {
                throw new UnknownDeviceException($this->request, 'Unknown device');
            } else {
                throw new BadCredentialsException($this->request, 'Bad credentials');
            }

            $apiKey = new ApiKey();
            $apiKey->userId = (int)$artist->id;
            $apiKey->setApiKey();
            $apiKey->validSince = new DateTime();

            if (!empty($userAgentHeader)) {
                $apiKey->userAgent = $userAgentHeader;
            } else {
                $apiKey->userAgent = 'unknown';
            }

            $apiKey->remoteAddress = $this->request->getHeaderLine(
                'X-Forwarded-For'
            ) ?: $_SERVER['REMOTE_ADDR'];
            $apiKey->create();

            $artist->twoFactorToken = null;
            $artist->update();

            try {
                $this->newLoginMail->sendMail($artist->email, $artist->artistName, $apiKey);
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
