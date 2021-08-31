<?php

namespace App\Web\Actions\File;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\File;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\ConflictException;
use App\Web\Exceptions\NoResultException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/media/file/{id}', JinyaAction::PUT)]
#[Authenticated(role: Authenticated::WRITER)]
#[OpenApiRequest('This action create a new file')]
#[OpenApiRequestBody([
    'name' => ['type' => 'string'],
])]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiRequestExample('File with required fields', [
    'name' => OpenApiResponse::FAKER_WORD,
])]
#[OpenApiResponse('Successfully updated the file', statusCode: Action::HTTP_CREATED, ref: File::class)]
#[OpenApiResponse('Name exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Name exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Name exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class UpdateFileAction extends Action
{

    /**
     * @inheritDoc
     * @throws JsonException
     * @throws ConflictException
     * @throws Exception
     */
    public function action(): Response
    {
        $body = $this->request->getParsedBody();
        $id = $this->args['id'];
        $file = File::findById($id);
        if ($file === null) {
            throw new NoResultException($this->request, 'File not found');
        }

        if (isset($body['name'])) {
            $file->name = $body['name'];
        }

        try {
            $file->update();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->noContent();
    }
}