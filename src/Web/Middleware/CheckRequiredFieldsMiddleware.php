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
    /** @var array<string> The required fields */
    private array $fields;

    /**
     * CheckFieldsMiddleware constructor.
     *
     * @param array<string> $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Processes the middleware, during the processing the request will be checked for all required fields
     *
     * @throws HttpNotImplementedException
     * @throws MissingFieldsException
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();

        if ($this->checkRequiredFields($body, $this->fields, $request)) {
            return $handler->handle($request);
        }

        throw new HttpNotImplementedException($request);
    }

    /**
     * Checks the required fields in the passed request body. If the fields don't match a MissingFieldsException is thrown
     *
     * @param array<string, mixed> $body
     * @param array<string> $requiredFields
     * @param Request $request
     * @return bool
     */
    private function checkRequiredFields(array $body, array $requiredFields, Request $request): bool
    {
        $bodyFields = array_keys($body);
        $intersectBody = array_intersect($bodyFields, $requiredFields);

        if (count($requiredFields) === count($intersectBody)) {
            return true;
        }

        throw new MissingFieldsException(
            $request,
            array_values(array_diff($requiredFields, array_values($intersectBody)))
        );
    }
}
