<?php

declare(strict_types=1);

namespace App\Web\Handlers;

use App\Web\Actions\Action;
use App\Web\Exceptions\MissingFieldsException;
use App\Web\Exceptions\MissingOneOfFieldsException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpSpecializedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * {@inheritdoc}
     * @throws JsonException
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        $this->logger->warning($exception);

        $data = [
            'success' => false,
            'error' => [
                'message' => $exception->getMessage(),
                'type' => array_reverse(explode('\\', get_class($exception)))[0],
            ],
        ];

        $code = Action::HTTP_INTERNAL_SERVER_ERROR;
        if ($exception instanceof MissingFieldsException || $exception instanceof MissingOneOfFieldsException) {
            $data = $exception;
            $code = Action::HTTP_BAD_REQUEST;
        }

        if ($exception instanceof HttpSpecializedException) {
            $code = $exception->getCode();
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}
