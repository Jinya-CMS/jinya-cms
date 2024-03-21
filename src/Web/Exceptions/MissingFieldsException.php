<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;

/**
 * This exception is thrown when a create request has missing fields
 */
class MissingFieldsException extends HttpException implements JsonSerializable
{
    /** @var array<string> The fields that are missing */
    public array $fields;

    /**
     * Creates a new MissingFieldsException, sets the fields and the response code to 400
     *
     * @param ServerRequestInterface $request
     * @param array<string> $fields
     */
    public function __construct(ServerRequestInterface $request, array $fields = [])
    {
        parent::__construct($request, 'Fields not found', Action::HTTP_BAD_REQUEST);
        $this->fields = $fields;
    }

    /**
     * JSON serializes the exception
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['success' => 'false', 'fields' => 'array'])] public function jsonSerialize(): array
    {
        return [
            'success' => false,
            'fields' => $this->fields,
        ];
    }
}
