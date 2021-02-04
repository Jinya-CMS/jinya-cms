<?php

namespace App\Web\Actions\Authentication;

use App\Database\Artist;
use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Mailing\Types\TwoFactorMail;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use JetBrains\PhpStorm\Pure;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

#[JinyaAction('/api/2fa', JinyaAction::POST)]
#[RequiredFields(['username', 'password'])]
#[OpenApiRequest('This action requests a new two factor code')]
#[OpenApiRequestBody([
    'username' => ['type' => 'string', 'format' => 'email'],
    'password' => ['type' => 'string', 'format' => 'password'],
])]
#[OpenApiRequestExample('Request two factor code', [
    'email' => OpenApiResponse::FAKER_EMAIL,
    'password' => OpenApiResponse::FAKER_PASSWORD,
])]
#[OpenApiResponse('Successfully requests two factor code', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Invalid credentials', statusCode: Action::HTTP_UNAUTHORIZED)]
class TwoFactorAction extends Action
{

    private TwoFactorMail $twoFactorMail;

    /**
     * TwoFactorAction constructor.
     * @param LoggerInterface $logger
     * @param TwoFactorMail $twoFactorMail
     */
    #[Pure] public function __construct(LoggerInterface $logger, TwoFactorMail $twoFactorMail)
    {
        parent::__construct($logger);
        $this->twoFactorMail = $twoFactorMail;
    }

    /**
     * @inheritDoc
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $artist = Artist::findByEmail($body['username']);
        if ($artist !== null && $artist->validatePassword($body['password'])) {
            $artist->setTwoFactorCode();
            $artist->update();

            $this->twoFactorMail->sendMail($artist->email, $artist->artistName, $artist->twoFactorToken);

            return $this->respond([], Action::HTTP_NO_CONTENT);
        }

        if ($artist !== null) {
            $artist->twoFactorToken = null;
            $artist->update();
        }

        return $this->respond([], Action::HTTP_UNAUTHORIZED);
    }
}