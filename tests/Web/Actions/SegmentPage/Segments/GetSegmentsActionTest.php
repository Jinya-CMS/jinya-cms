<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\Segment;
use App\Database\SegmentPage;
use App\Web\Actions\SegmentPage\Segments\GetSegmentsAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class GetSegmentsActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $segment = new Segment();
        $segment->html = '';
        $segment->position = 0;
        $segment->pageId = $segmentPage->getIdAsInt();
        $segment->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetSegmentsAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);

        self::assertEquals(200, $result->getStatusCode());
    }

    public function test__invokeSegmentNotFound(): void
    {
        $this->expectExceptionMessage('Segment page not found');
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetSegmentsAction();
        $action($request, $response, ['id' => -1]);
    }
}
