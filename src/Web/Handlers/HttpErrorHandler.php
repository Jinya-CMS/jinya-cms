<?php

namespace App\Web\Handlers;

use App\Web\Actions\Action;
use App\Web\Exceptions\MissingFieldsException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpSpecializedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

/**
 * A Slim error handler to respond if the request fails a specialized JSON error
 */
class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * Returns a specialized JSON response containing all information about the error data
     *
     * @throws JsonException
     */
    protected function respond(): Response
    {
        $exception = $this->exception;
        try {
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        } catch (Throwable) {
        }

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
            $code = Action::HTTP_BAD_REQUEST;
        }

        if ($exception instanceof HttpSpecializedException) {
            $code = $exception->getCode();
            $this->logger->error($exception->getMessage());
            $this->logger->error($exception->getTraceAsString());
        } elseif ($exception instanceof InvalidQueryException) {
            $code = 409;
            $data['error']['message'] = $exception->errorInfo[2] ?? $exception->getMessage();
        }

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($data, JSON_THROW_ON_ERROR));

        return $response->withHeader('Content-Type', 'application/json')->withStatus($code);
    }
}
