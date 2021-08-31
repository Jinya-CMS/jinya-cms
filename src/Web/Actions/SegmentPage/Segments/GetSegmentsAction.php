<?php

namespace App\Web\Actions\SegmentPage\Segments;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\FormItem;
use App\Database\SegmentPage;
use App\OpenApiGeneration\Attributes\OpenApiArrayResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/segment-page/{id}/segment', JinyaAction::GET)]
#[Authenticated(Authenticated::READER)]
#[OpenApiRequest('This action gets all segments for the given segment page')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiArrayResponse('Successfully got the segments', example: [
    'file' => [
        'id' => 0,
        'name' => OpenApiResponse::FAKER_WORD,
        'type' => OpenApiResponse::FAKER_MIMETYPE,
        'path' => OpenApiResponse::FAKER_SHA1,
    ],
    'id' => 0,
    'position' => 0,
    'action' => 'link',
    'target' => OpenApiResponse::FAKER_URL,
], exampleName: 'List of segments', ref: FormItem::class)]
#[OpenApiResponse('Segment page not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Segment page not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetSegmentsAction extends Action
{
    /**
     * {@inheritDoc}
     * @return Response
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     */
    public function action(): Response
    {
        $id = $this->args['id'];
        $segments = SegmentPage::findById($id)->getSegments();

        return $this->respond($this->formatIterator($segments));
    }
}
