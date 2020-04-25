<?php
declare(strict_types=1);

namespace App\Web\Handlers;

use App\Web\Actions\Action;
use App\Web\Exceptions\MissingFieldsException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpSpecializedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     * @throws JsonException
     */
    protected function respond(): Response
    {
        $exception = $this->exception;

        $data = [
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'type' => array_reverse(explode('\\', get_class($exception)))[0],
            ],
        ];

        $code = Action::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof MissingFieldsException) {
            $data = $exception;
        }
        if ($exception instanceof HttpSpecializedException) {
            $code = $exception->getCode();
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR, 512));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}
