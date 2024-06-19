<?php

namespace Jinya\Cms\Web\Controllers;

use DateInterval;
use DateTime;
use Jinya\Cms\Authentication\AuthenticationChecker;
use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Database\ApiKey;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\KnownDevice;
use Jinya\Cms\Database\TotpMode;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Mailing\Types\NewLoginMail;
use Jinya\Cms\Mailing\Types\NewSavedDeviceMail;
use Jinya\Cms\Mailing\Types\TwoFactorMail;
use Jinya\Cms\Utils\CookieSetter;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Cms\Web\Middleware\CheckRequiredFieldsMiddleware;
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
    private const DEVICE_CODE_COOKIE = 'JinyaDeviceCode';

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
    #[Route(HttpMethod::PUT, 'api/account/password')]
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
    #[Route(HttpMethod::POST, 'api/login')]
    #[Middlewares(new CheckRequiredFieldsMiddleware(['password', 'username']))]
    public function login(): ResponseInterface
    {
        $username = $this->body['username'] ?? '';
        $password = $this->body['password'] ?? '';
        $twoFactorCode = $this->body['twoFactorCode'] ?? '';
        $knownDeviceCode = $this->request->getCookieParams()[self::DEVICE_CODE_COOKIE] ?? $this->getHeader(
            self::DEVICE_CODE_COOKIE
        );

        $artist = Artist::findByEmail($username);
        if ($artist !== null && $artist->validatePassword($password)) {
            $userAgentHeader = $this->getHeader('User-Agent');
            $remoteAddress = $this->getHeader('X-Forwarded-For') ?: $this->request->getServerParams()['REMOTE_ADDR'];
            if (!empty($knownDeviceCode) && $artist->validateDevice($knownDeviceCode)) {
                $knownDevice = KnownDevice::findByCode($knownDeviceCode);
            } elseif ($artist->verifyTotpCode($twoFactorCode)) {
                $knownDevice = new KnownDevice();
                $knownDevice->userId = $artist->id;
                $knownDevice->remoteAddress = $remoteAddress;
                if (!empty($userAgentHeader)) {
                    $knownDevice->userAgent = $userAgentHeader;
                } else {
                    $knownDevice->userAgent = 'unknown';
                }
                $knownDevice->create();
                if ($artist->newDeviceMailEnabled) {
                    try {
                        $this->newSavedDeviceMail->sendMail($artist->email, $artist->artistName, $knownDevice);
                    } catch (Throwable $exception) {
                        $this->logger->warning($exception->getMessage());
                    }
                }
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

            if ($artist->loginMailEnabled) {
                try {
                    $this->newLoginMail->sendMail($artist->email, $artist->artistName, $apiKey);
                } catch (Throwable $exception) {
                    $this->logger->warning($exception->getMessage());
                }
            }

            $response = $this->json([
                'apiKey' => $apiKey->apiKey,
                'deviceCode' => $knownDevice->deviceKey,
                'roles' => $artist->roles,
            ]);

            $apiKeyExpires = JinyaConfiguration::getConfiguration()->get('api_key_expiry', 'jinya', 86400);

            return CookieSetter::setCookie(
                CookieSetter::setCookie($response, self::DEVICE_CODE_COOKIE, $knownDevice->deviceKey, httpOnly: false),
                AuthenticationChecker::AUTHENTICATION_COOKIE_NAME,
                $apiKey->apiKey,
                $apiKey->validSince->add(new DateInterval("PT{$apiKeyExpires}S"))
            );
        }

        return $this->badCredentialsResponse;
    }

    /**
     * @throws NotNullViolationException
     * @throws Exception
     * @throws Throwable
     */
    #[Route(HttpMethod::POST, 'api/2fa')]
    #[Middlewares(new CheckRequiredFieldsMiddleware(['password', 'username']))]
    public function twoFactorCode(): ResponseInterface
    {
        $artist = Artist::findByEmail($this->body['username']);
        if ($artist !== null && $artist->validatePassword($this->body['password'])) {
            if ($artist->totpMode === TotpMode::Email) {
                $artist->setTwoFactorCode();
                $artist->update();

                $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);
            }

            return $this->noContent();
        }

        if ($artist !== null) {
            $artist->twoFactorToken = null;
            $artist->update();
        }

        return $this->badCredentialsResponse;
    }

    /**
     * @return ResponseInterface
     * @codeCoverageIgnore
     */
    #[Route(HttpMethod::HEAD, 'api/login')]
    #[Middlewares(new AuthorizationMiddleware())]
    public function validateLogin(): ResponseInterface
    {
        return $this->noContent();
    }
}
