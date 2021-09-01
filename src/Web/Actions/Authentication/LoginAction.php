<?php

namespace App\Web\Actions\Authentication;

use App\Database\ApiKey;
use App\Database\Artist;
use App\Database\KnownDevice;
use App\Mailing\Types\NewLoginMail;
use App\Mailing\Types\NewSavedDeviceMail;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\BadCredentialsException;
use App\Web\Exceptions\UnknownDeviceException;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Throwable;

#[JinyaAction('/api/login', JinyaAction::POST)]
#[RequiredFields(['username', 'password'])]
#[OpenApiRequest('This action creates a new api key')]
#[OpenApiParameter('JinyaDeviceCode', false, in: OpenApiParameter::IN_HEADER)]
#[OpenApiRequestBody([
    'username' => ['type' => 'string', 'format' => 'email'],
    'password' => ['type' => 'string', 'format' => 'password'],
    'twoFactorCode' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Login with two factor code', [
    'email' => OpenApiResponse::FAKER_EMAIL,
    'password' => OpenApiResponse::FAKER_PASSWORD,
    'twoFactorCode' => OpenApiResponse::FAKER_NUMERIFY,
])]
#[OpenApiRequestExample('Login without two factor code', [
    'email' => OpenApiResponse::FAKER_EMAIL,
    'password' => OpenApiResponse::FAKER_PASSWORD,
])]
#[OpenApiResponse('Successfully logged in', example: [
    'apiKey' => OpenApiResponse::FAKER_SHA1,
    'deviceCode' => OpenApiResponse::FAKER_SHA1,
    'roles' => ['ROLE_READER'],
], exampleName: 'API key response', schema: [
    'apiKey' => ['type' => 'string'],
    'deviceCode' => ['type' => 'string'],
    'roles' => ['type' => 'array', 'items' => ['type' => 'string']],
])]
#[OpenApiResponse('Invalid credentials', example: [
    'success' => false,
    'error' => [
        'message' => 'Bad credentials',
        'type' => 'BadCredentialsException',
    ],
], exampleName: 'Invalid credentials', statusCode: Action::HTTP_FORBIDDEN, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
#[OpenApiResponse('Invalid device code', example: [
    'success' => false,
    'error' => [
        'message' => 'Unknown device',
        'type' => 'UnknownDeviceException',
    ],
], exampleName: 'Invalid device code', statusCode: Action::HTTP_UNAUTHORIZED, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
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
        $body = $this->request->getParsedBody();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';
        $twoFactorCode = $body['twoFactorCode'] ?? '';
        $knownDeviceCode = $this->request->getHeaderLine('JinyaDeviceCode');

        $artist = Artist::findByEmail($username);
        if (null !== $artist && $artist->validatePassword($password)) {
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
