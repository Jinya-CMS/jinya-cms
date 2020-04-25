<?php

namespace App\Web\Middleware;

use App\Web\Exceptions\MissingFieldsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Exception\HttpNotImplementedException;

class CheckRequiredFieldsMiddleware
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
     * Middleware to check for required fields in the parsed body
     *
     * @param Request $request PSR-7 request
     * @param RequestHandler $handler PSR-15 request handler
     * @return ResponseInterface
     * @throws HttpNotImplementedException
     * @throws MissingFieldsException
     */
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $body = $request->getParsedBody();

        if ($this->checkRequiredFields($body, $this->fields, $request)) {
            return $handler->handle($request);
        }

        throw new HttpNotImplementedException($request);
    }

    private function checkRequiredFields(array $body, array $requiredFields, Request $request): bool
    {
        $bodyFields = array_keys($body);
        $intersectBody = array_intersect($bodyFields, $requiredFields);

        if (count($requiredFields) === count($intersectBody)) {
            return true;
        }

        throw new MissingFieldsException($request, array_values(array_diff($requiredFields, array_values($intersectBody))));
    }
}