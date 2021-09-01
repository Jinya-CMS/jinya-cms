<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Segment;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;

#[JinyaAction('/api/segment-page/{id}/segment/{position}', JinyaAction::DELETE)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action deletes the given segment')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('position', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully deleted the segment', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Segment page or segment not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page or segment not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class DeleteSegmentAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws HttpNotFoundException
     * @throws JsonException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    protected function action(): Response
    {
        $segment = Segment::findByPosition($this->args['id'], $this->args['position']);
        if (!$segment) {
            throw new HttpNotFoundException($this->request, 'Segment not found');
        }

        $segment->delete();

        return $this->noContent();
    }
}
