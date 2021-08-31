<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/media/file/{id}', JinyaAction::GET)]
#[Authenticated(role: Authenticated::READER)]
#[OpenApiRequest('This action gets the given file')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the file', example: [
    'path' => OpenApiResponse::FAKER_SHA1,
    'name' => OpenApiResponse::FAKER_WORD,
    'type' => OpenApiResponse::FAKER_MIMETYPE,
    'id' => 1,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Returned file', ref: File::class)]
#[OpenApiResponse('Artist not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'File not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetFileByIdAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws HttpNotFoundException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $file = File::findById((int)$this->args['id']);
        if (null === $file) {
            throw new HttpNotFoundException($this->request, 'Artist not found');
        }

        return $this->respond($file->format());
    }
}
