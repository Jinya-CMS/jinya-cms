<?php
declare(strict_types=1);

namespace App\Web\Handlers;

use App\Web\Actions\ActionError;
use App\Web\Actions\ActionPayload;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpException;
use Slim\Exception\HttpForbiddenException;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Exception\HttpNotImplementedException;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Throwable;

class HttpErrorHandler extends SlimErrorHandler
{
    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $exception = $this->exception;

        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($exception, JSON_THROW_ON_ERROR, 512));

        return $response->withHeader('Content-Type', 'application/json');
    }
}
