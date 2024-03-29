<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\Gallery;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\CreateGallerySegmentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateGallerySegmentActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $gallery = new Gallery();
        $gallery->name = Uuid::uuid();
        $gallery->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'gallery' => $gallery->getIdAsInt(),
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateGallerySegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeSegmentPageNotFound(): void
    {
        $this->expectExceptionMessage('Segment page not found');
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CreateGallerySegmentAction();
        $action($request, $response, ['id' => -1]);
    }

    public function test__invokeGalleryNotFound(): void
    {
        $this->expectExceptionMessage('Gallery not found');
        $this->expectException(NoResultException::class);
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'gallery' => -1,
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateGallerySegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);
    }
}
