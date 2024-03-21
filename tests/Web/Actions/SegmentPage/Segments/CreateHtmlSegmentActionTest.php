<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\CreateHtmlSegmentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateHtmlSegmentActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'html' => Uuid::uuid(),
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateHtmlSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeSegmentPageNotFound(): void
    {
        $this->expectExceptionMessage('Segment page not found');
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CreateHtmlSegmentAction();
        $action($request, $response, ['id' => -1]);
    }
}
