<?php

namespace App\Web\Actions\Authentication;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Web\Actions\Action;
use App\Web\Exceptions\BadCredentialsException;
use App\Web\Exceptions\UnknownDeviceException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;

class LoginAction extends Action
{

    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $username = $body['username'];
        $password = $body['password'];
        $twoFactorCode = $body['twoFactorCode'];
        $knownDeviceCode = $this->request->getHeaderLine('JinyaDeviceCode');

        $artist = Artist::findByEmail($username);
        if ($artist !== null && $artist->validatePassword($password)) {
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
                $apiKey->userAgent = $userAgentHeader[0];
                $knownDevice->userAgent = $userAgentHeader[0];
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

            return $this->respond([
                'apiKey' => $apiKey->apiKey,
                'deviceCode' => $knownDevice->deviceKey,
                'roles' => $artist->roles,
            ]);
        }

        throw new BadCredentialsException($this->request, 'Bad credentials');
    }
}