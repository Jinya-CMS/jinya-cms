<?php

namespace App\Web\Middleware;

use App\Web\Handlers\ErrorHandler;
use Jinya\Router\Extensions\Database\Exceptions\MissingFieldsException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Middleware to check for required fields in the parsed body
 */
readonly class CheckRequiredFieldsMiddleware implements MiddlewareInterface
{
    /**
     * CheckFieldsMiddleware constructor.
     *
     * @param array<string> $fields The required fields
     */
    public function __construct(public array $fields)
    {
    }

    /**
     * Processes the middleware, during the processing the request will be checked for all required fields
     * @throws JsonException
     */
    public function process(Request $request, RequestHandler $handler): ResponseInterface
    {
        /** @var array<string, mixed> $body */
        $body = $request->getParsedBody();

        try {
            $this->checkRequiredFields($body, $this->fields, $request);

            return $handler->handle($request);
        } catch (MissingFieldsException $exception) {
            /** @var ResponseInterface $response */
            $response = (new ErrorHandler())->handleMissingFieldsError($request, $exception);

            return $response;
        }
    }

    /**
     * Checks the required fields in the given request body. If the fields don't match, a MissingFieldsException is thrown
     *
     * @param array<string, mixed> $body
     * @param array<string> $requiredFields
     * @param Request $request
     * @return void
     * @throws MissingFieldsException
     */
    private function checkRequiredFields(array $body, array $requiredFields, Request $request): void
    {
        $bodyFields = array_keys($body);
        $intersectBody = array_intersect($bodyFields, $requiredFields);

        if (count($requiredFields) !== count($intersectBody)) {
            throw new MissingFieldsException(
                $request,
                array_values(array_diff($requiredFields, array_values($intersectBody))),
                'There are required fields missing'
            );
        }
    }
}
