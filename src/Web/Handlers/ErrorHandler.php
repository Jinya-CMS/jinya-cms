<?php

namespace Jinya\Cms\Web\Handlers;

use Jinya\Cms\Logging\Logger;
use Jinya\Router\Extensions\Database\ErrorHandler as RouterErrorHandler;
use Jinya\Router\Extensions\Database\Exceptions\CreateColumnIsNullException;
use Jinya\Router\Extensions\Database\Exceptions\CreateReferenceFailedException;
use Jinya\Router\Extensions\Database\Exceptions\CreateUniqueFailedException;
use Jinya\Router\Extensions\Database\Exceptions\DeleteReferencedException;
use Jinya\Router\Extensions\Database\Exceptions\InvalidDateFormatException;
use Jinya\Router\Extensions\Database\Exceptions\MissingFieldsException;
use Jinya\Router\Extensions\Database\Exceptions\NotFoundException;
use Jinya\Router\Extensions\Database\Exceptions\UpdateColumnIsNullException;
use Jinya\Router\Extensions\Database\Exceptions\UpdateReferenceFailedException;
use Jinya\Router\Extensions\Database\Exceptions\UpdateUniqueFailedException;
use JsonException;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class ErrorHandler implements RouterErrorHandler
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * @throws JsonException
     */
    public function handleNotFound(
        ServerRequestInterface $request,
        NotFoundException $notFoundException
    ): ResponseInterface|false {
        $this->logger->info($notFoundException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $notFoundException->getMessage(),
                'type' => 'not-found',
            ],
        ];

        return new Response(404, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleInternalServerError(
        ServerRequestInterface $request,
        Throwable $throwable
    ): ResponseInterface|false {
        $this->logger->error($throwable->getMessage());
        $this->logger->error($throwable->getTraceAsString());
        $data = [
            'success' => false,
            'error' => [
                'message' => $throwable->getMessage(),
                'type' => 'internal-server-error',
            ],
        ];

        return new Response(500, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleDeleteReferencedError(
        ServerRequestInterface $request,
        DeleteReferencedException $deleteReferencedException
    ): ResponseInterface|false {
        $this->logger->info($deleteReferencedException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $deleteReferencedException->getMessage(),
                'type' => 'entity-referenced',
            ],
        ];

        return new Response(409, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleMissingFieldsError(
        ServerRequestInterface $request,
        MissingFieldsException $missingFieldsException
    ): ResponseInterface|false {
        $this->logger->info($missingFieldsException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $missingFieldsException->getMessage(),
                'type' => 'missing-fields',
                'missingFields' => $missingFieldsException->missingFields,
            ],
        ];

        return new Response(400, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleCreateReferenceFailedError(
        ServerRequestInterface $request,
        CreateReferenceFailedException $createReferenceFailedException
    ): ResponseInterface|false {
        $this->logger->info($createReferenceFailedException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $createReferenceFailedException->getMessage(),
                'type' => 'reference-not-found',
            ],
        ];

        return new Response(409, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleCreateColumnIsNullError(
        ServerRequestInterface $request,
        CreateColumnIsNullException $createColumnIsNullException
    ): ResponseInterface|false {
        $this->logger->info($createColumnIsNullException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $createColumnIsNullException->getMessage(),
                'type' => 'not-null',
            ],
        ];

        return new Response(400, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleCreateUniqueFailedError(
        ServerRequestInterface $request,
        CreateUniqueFailedException $createUniqueFailedException
    ): ResponseInterface|false {
        $this->logger->info($createUniqueFailedException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $createUniqueFailedException->getMessage(),
                'type' => 'unique-failed',
            ],
        ];

        return new Response(409, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleUpdateReferenceFailedError(
        ServerRequestInterface $request,
        UpdateReferenceFailedException $updateReferenceFailedException
    ): ResponseInterface|false {
        $this->logger->info($updateReferenceFailedException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $updateReferenceFailedException->getMessage(),
                'type' => 'reference-not-found',
            ],
        ];

        return new Response(409, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleUpdateColumnIsNullError(
        ServerRequestInterface $request,
        UpdateColumnIsNullException $updateColumnIsNullException
    ): ResponseInterface|false {
        $this->logger->info($updateColumnIsNullException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $updateColumnIsNullException->getMessage(),
                'type' => 'not-null',
            ],
        ];

        return new Response(400, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleUpdateUniqueFailedError(
        ServerRequestInterface $request,
        UpdateUniqueFailedException $updateUniqueFailedException
    ): ResponseInterface|false {
        $this->logger->info($updateUniqueFailedException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $updateUniqueFailedException->getMessage(),
                'type' => 'unique-failed',
            ],
        ];

        return new Response(409, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }

    /**
     * @throws JsonException
     */
    public function handleInvalidDateFormatError(
        ServerRequestInterface $request,
        InvalidDateFormatException $invalidDateFormatException
    ): ResponseInterface|false {
        $this->logger->info($invalidDateFormatException->getMessage());
        $data = [
            'success' => false,
            'error' => [
                'message' => $invalidDateFormatException->getMessage(),
                'type' => 'invalid-date-format',
            ],
        ];

        return new Response(400, headers: ['Content-Type' => 'application/json'], body: json_encode(
            $data,
            JSON_THROW_ON_ERROR
        ));
    }
}
