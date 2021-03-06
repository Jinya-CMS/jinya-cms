<?php

namespace App\Web\Actions\SegmentPage;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\SegmentPage;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given segment page')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the segment page', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteSegmentPageAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $segmentPage = SegmentPage::findById($this->args['id']);
        if (null === $segmentPage) {
            throw new NoResultException($this->request, 'Segment page not found');
        }

        $segmentPage->delete();

        return $this->noContent();
    }
}
