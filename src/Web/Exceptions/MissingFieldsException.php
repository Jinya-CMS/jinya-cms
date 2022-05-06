<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;

/**
 *
 */
class MissingFieldsException extends HttpException implements JsonSerializable
{
    /**
     * @var array<string>
     */
    public array $fields;

    /**
     * @param ServerRequestInterface $request
     * @param array<string> $fields
     */
    public function __construct(ServerRequestInterface $request, array $fields = [])
    {
        parent::__construct($request, 'Fields not found', Action::HTTP_BAD_REQUEST);
        $this->fields = $fields;
    }

    /**
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