<?php

namespace App\Web\Middleware;

use App\Web\Exceptions\MissingOneOfFieldsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotImplementedException;

/**
 * Middleware to check for required fields in the parsed body
 */
class CheckRequiredOneOfFieldsMiddleware implements MiddlewareInterface
{
    private array $fields;

    /**
     * CheckFieldsMiddleware constructor.
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if ($this->checkRequiredFields($body, $this->fields, $request)) {
            return $handler->handle($request);
        }

        throw new HttpNotImplementedException($request);
    }

    /**
     * @param array $body
     * @param array $requiredFields
     * @param Request $request
     * @return bool
     * @throws MissingOneOfFieldsException
     */
    private function checkRequiredFields(array $body, array $requiredFields, Request $request): bool
    {
        $bodyFields = array_keys($body);
        foreach ($requiredFields as $requiredField) {
            if (in_array($requiredField, $bodyFields, true)) {
                return true;
            }
        }

        throw new MissingOneOfFieldsException($request, $requiredFields);
    }
}