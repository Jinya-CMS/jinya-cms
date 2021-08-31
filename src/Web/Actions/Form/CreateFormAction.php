<?php

namespace App\Web\Actions\Form;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
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
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/form', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['title', 'toAddress'])]
#[OpenApiRequest('This action create a new form')]
#[OpenApiRequestBody([
    'title' => ['type' => 'string'],
    'toAddress' => ['type' => 'string'],
    'description' => ['type' => 'string'],
])]
#[OpenApiRequestExample('Form with required fields', [
    'title' => OpenApiResponse::FAKER_WORD,
    'toAddress' => OpenApiResponse::FAKER_EMAIL,
])]
#[OpenApiRequestExample('Form with all fields', [
    'title' => OpenApiResponse::FAKER_WORD,
    'toAddress' => OpenApiResponse::FAKER_EMAIL,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
])]
#[OpenApiResponse('Successfully created the form', example: [
    'id' => 1,
    'title' => OpenApiResponse::FAKER_WORD,
    'toAddress' => OpenApiResponse::FAKER_EMAIL,
    'description' => OpenApiResponse::FAKER_PARAGRAPH,
    'created' => OpenApiResponse::MODIFICATION_EXAMPLE,
    'updated' => OpenApiResponse::MODIFICATION_EXAMPLE,
], exampleName: 'Created form', statusCode: Action::HTTP_CREATED, ref: Form::class)]
#[OpenApiResponse('Title exists', example: [
    'success' => false,
    'error' => [
        'message' => 'Title exists',
        'type' => 'ConflictException',
    ],
], exampleName: 'Title exists', statusCode: Action::HTTP_CONFLICT, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CreateFormAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    public function action(): Response
    {
        $body = $this->request->getParsedBody();
        $form = new Form();
        $form->title = $body['title'];
        $form->description = $body['description'] ?? '';
        $form->toAddress = $body['toAddress'];

        try {
            $form->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Title already used');
        }

        return $this->respond($form->format(), Action::HTTP_CREATED);
    }
}