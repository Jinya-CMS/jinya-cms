<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\ModernPageSection;
use App\Database\ModernPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\GetSegmentsAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetSegmentsActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $segmentPage = new ModernPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $segment = new ModernPageSection();
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
