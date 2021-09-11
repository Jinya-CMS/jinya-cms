<?php

namespace App\Web\Actions\Gallery;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Gallery;
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

#[JinyaAction('/api/media/gallery', JinyaAction::POST)]
#[Authenticated(role: Authenticated::WRITER)]
#[RequiredFields(['name'])]
class CreateGalleryAction extends Action
{
    /**
     * @inheritDoc
     * @throws Exception
     */
    protected function action(): Response
    {
        $body = $this->request->getParsedBody();
        $gallery = new Gallery();
        $gallery->name = $body['name'];

        if (isset($body['description'])) {
            $gallery->description = $body['description'];
        }

        if (isset($body['orientation'])) {
            $gallery->orientation = $body['orientation'];
        }

        if (isset($body['type'])) {
            $gallery->type = $body['type'];
        }

        try {
            $gallery->create();
        } catch (UniqueFailedException) {
            throw new ConflictException($this->request, 'Name already used');
        }

        return $this->respond($gallery->format(), Action::HTTP_CREATED);
    }
}