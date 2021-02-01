<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Exceptions\ConflictException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['name'])]
#[OpenApiRequest('This action create a new file')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
])]
#[OpenApiRequestExample('File with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiResponse('Successfully created the file', example: [
    'path' => OpenApiResponse::FAKER_SHA1,
    'name' => OpenApiResponse::FAKER_WORD,
    'type' => OpenApiResponse::FAKER_MIMETYPE,
    'id' => 1,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Returned file', statusCode: Action::HTTP_CREATED, ref: File::class)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateFileAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws ConflictException
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $file = new File();
        $file->name = $body['name'];
        try {
            $file->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }


        return $this->respond($file->format(), Action::HTTP_CREATED);
    }
}