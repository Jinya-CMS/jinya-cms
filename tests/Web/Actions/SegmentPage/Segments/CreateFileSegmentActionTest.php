<?php

namespace Jinya\Tests\Web\Actions\SegmentPage\Segments;

use App\Database\File;
use App\Database\SegmentPage;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\SegmentPage\Segments\CreateFileSegmentAction;
use App\Web\Exceptions\NoResultException;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class CreateFileSegmentActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $file = new File();
        $file->name = Uuid::uuid();
        $file->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'file' => $file->getIdAsInt(),
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateFileSegmentAction();
        $result = $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);

        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeSegmentPageNotFound(): void
    {
        $this->expectExceptionMessage('Segment page not found');
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new CreateFileSegmentAction();
        $action($request, $response, ['id' => -1]);
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectExceptionMessage('File not found');
        $this->expectException(NoResultException::class);
        $segmentPage = new SegmentPage();
        $segmentPage->name = Uuid::uuid();
        $segmentPage->create();

        $request = new ServerRequest('', '');
        $request = $request->withParsedBody([
            'file' => -1,
            'position' => 0,
        ]);
        $response = new Response();
        $action = new CreateFileSegmentAction();
        $action($request, $response, ['id' => $segmentPage->getIdAsInt()]);
    }
}
