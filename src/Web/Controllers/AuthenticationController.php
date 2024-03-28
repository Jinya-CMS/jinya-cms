<?php

namespace App\Web\Controllers;

use App\Authentication\CurrentUser;
use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Logging\Logger;
use App\Mailing\Types\NewLoginMail;
use App\Mailing\Types\NewSavedDeviceMail;
use App\Mailing\Types\TwoFactorMail;
use App\Web\Middleware\AuthorizationMiddleware;
use App\Web\Middleware\CheckRequiredFieldsMiddleware;
use DateTime;
use Jinya\Database\Exception\NotNullViolationException;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use JsonException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

#[Controller]
class AuthenticationController extends BaseController
{
    private readonly LoggerInterface $logger;
    private readonly ResponseInterface $badCredentialsResponse;

    /**
     * Creates a new LoginAction and initializes the mail factories
     * @param NewLoginMail $newLoginMail
     * @param NewSavedDeviceMail $newSavedDeviceMail
     * @param TwoFactorMail $twoFactorMail
     * @throws JsonException
     */
    public function __construct(
        private readonly NewLoginMail $newLoginMail = new NewLoginMail(),
        private readonly NewSavedDeviceMail $newSavedDeviceMail = new NewSavedDeviceMail(),
        private readonly TwoFactorMail $twoFactorMail = new TwoFactorMail()
    ) {
        $this->logger = Logger::getLogger();
        $this->badCredentialsResponse = $this->json([
            'success' => false,
            'error' => [
                'message' => 'Bad credentials',
                'type' => 'bad-credentials'
            ]
        ], self::HTTP_UNAUTHORIZED);
    }

    /**
     * @return ResponseInterface
     * @throws NotNullViolationException
     * @throws JsonException
     */
    #[Route(HttpMethod::PUT, '/api/account/password')]
    #[Middlewares(new AuthorizationMiddleware(), new CheckRequiredFieldsMiddleware(['password', 'oldPassword']))]
    public function changePassword(): ResponseInterface
    {
        $password = $this->body['password'];
        $oldPassword = $this->body['oldPassword'];
        if (!CurrentUser::$currentUser->changePassword($oldPassword, $password)) {
            return $this->json([
                'success' => false,
                'error' => [
                    'message' => 'The old password is wrong',
                    'type' => 'wrong-password'
                ]
            ], self::HTTP_FORBIDDEN);
        }

        return $this->noContent();
    }

    /**
     * @return ResponseInterface
     * @throws JsonException
     * @throws NotNullViolationException
     * @throws \Exception
     */
    #[Route(HttpMethod::POST, '/api/login')]
    #[Middlewares(new CheckRequiredFieldsMiddleware(['password', 'username']))]
    public function login(): ResponseInterface
    {
        $username = $this->body['username'] ?? '';
        $password = $this->body['password'] ?? '';
        $twoFactorCode = $this->body['twoFactorCode'] ?? '';
        $knownDeviceCode = $this->getHeader('JinyaDeviceCode');

        $artist = Artist::findByEmail($username);
        if ($artist !== null && $artist->validatePassword($password)) {
            $userAgentHeader = $this->getHeader('User-Agent');
            $remoteAddress = $this->getHeader('X-Forwarded-For') ?: $this->request->getServerParams()['REMOTE_ADDR'];
            if (!empty($knownDeviceCode) && $artist->validateDevice($knownDeviceCode)) {
                $knownDevice = KnownDevice::findByCode($knownDeviceCode);
            } elseif ($artist->twoFactorToken !== null && $artist->twoFactorToken !== '' && $artist->twoFactorToken === $twoFactorCode) {
                $knownDevice = new KnownDevice();
                $knownDevice->userId = $artist->id;
                $knownDevice->remoteAddress = $remoteAddress;
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
            } elseif (empty($knownDeviceCode) && !empty($twoFactorCode)) {
                return $this->badCredentialsResponse;
            } else {
                return $this->badCredentialsResponse;
            }

            $apiKey = new ApiKey();
            $apiKey->userId = $artist->id;
            $apiKey->setApiKey();
            $apiKey->validSince = new DateTime();

            if (!empty($userAgentHeader)) {
                $apiKey->userAgent = $userAgentHeader;
            } else {
                $apiKey->userAgent = 'unknown';
            }

            $apiKey->remoteAddress = $remoteAddress;
            $apiKey->create();

            $artist->twoFactorToken = null;
            $artist->update();

            try {
                $this->newLoginMail->sendMail($artist->email, $artist->artistName, $apiKey);
            } catch (Throwable $exception) {
                $this->logger->warning($exception->getMessage());
            }

            return $this->json([
                'apiKey' => $apiKey->apiKey,
                'deviceCode' => $knownDevice->deviceKey,
                'roles' => $artist->roles,
            ]);
        }

        return $this->badCredentialsResponse;
    }

    /**
     * @throws NotNullViolationException
     * @throws Exception
     * @throws Throwable
     */
    #[Route(HttpMethod::POST, '/api/2fa')]
    #[Middlewares(new CheckRequiredFieldsMiddleware(['password', 'username']))]
    public function twoFactorCode(): ResponseInterface
    {
        $artist = Artist::findByEmail($this->body['username']);
        if ($artist !== null && $artist->validatePassword($this->body['password'])) {
            $artist->setTwoFactorCode();
            $artist->update();

            $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);

            return $this->noContent();
        }

        if ($artist !== null) {
            $artist->twoFactorToken = null;
            $artist->update();
        }

        return $this->badCredentialsResponse;
    }

    #[Route(HttpMethod::HEAD, '/api/login')]
    #[Middlewares(new AuthorizationMiddleware())]
    public function validateLogin(): ResponseInterface
    {
        return $this->noContent();
    }
}
