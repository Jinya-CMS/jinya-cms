<?php

namespace App\Web\Middleware;

use App\Web\Exceptions\MissingFieldsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotImplementedException;

/**
 * Middleware to check for required fields in the parsed body
 */
class CheckRequiredFieldsMiddleware implements MiddlewareInterface
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

    /**
     * @param array $body
     * @param array $requiredFields
     * @param Request $request
     * @return bool
     * @throws MissingFieldsException
     */
    private function checkRequiredFields(array $body, array $requiredFields, Request $request): bool
    {
        $bodyFields = array_keys($body);
        $intersectBody = array_intersect($bodyFields, $requiredFields);

        if (count($requiredFields) === count($intersectBody)) {
            return true;
        }

        throw new MissingFieldsException($request, array_values(array_diff($requiredFields, array_values($intersectBody))));
    }

    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if ($this->checkRequiredFields($body, $this->fields, $request)) {
            return $handler->handle($request);
        }

        throw new HttpNotImplementedException($request);
    }
}