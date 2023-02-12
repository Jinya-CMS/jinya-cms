<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\Segment;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\DeleteSegmentAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpNotFoundException;

class DeleteSegmentActionTest extends DatabaseAwareTestCase
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
        $action = new DeleteSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt(), 'position' => 0]);

        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeSegmentNotFound(): void
    {
        $this->expectExceptionMessage('Segment not found');
        $this->expectException(HttpNotFoundException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeleteSegmentAction();
        $action($request, $response, ['id' => -1, 'position' => -1]);
    }
}
