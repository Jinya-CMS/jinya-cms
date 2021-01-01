<?php

namespace App\Web\Exceptions;

use App\Web\Actions\Action;
use JetBrains\PhpStorm\ArrayShape;
use JsonSerializable;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpException;

class MissingOneOfFieldsException extends HttpException implements JsonSerializable
{
    public array $fields;

    public function __construct(ServerRequestInterface $request, array $fields = [])
    {
        parent::__construct($request, 'One of the fields must be set', Action::HTTP_BAD_REQUEST);
        $this->fields = $fields;
    }

    #[ArrayShape(['success' => "false", 'fields' => "array"])] public function jsonSerialize()
    {
        return [
            'success' => false,
            'fields' => $this->fields,
        ];
    }
}