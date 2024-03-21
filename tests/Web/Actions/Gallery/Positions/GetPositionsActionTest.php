<?php

namespace Jinya\Tests\Web\Actions\Gallery\Positions;

use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Gallery\Positions\GetPositionsAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetPositionsActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $position = new GalleryFilePosition();
        $position->position = 0;
        $position->fileId = $file->getIdAsInt();
        $position->galleryId = $gallery->getIdAsInt();
        $position->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetPositionsAction();
        $result = $action($request, $response, ['galleryId' => $gallery->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertCount(1, json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR));
    }

    public function test__invokeGalleryNotFound(): void
    {
        $this->expectException(NoResultException::class);

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetPositionsAction();
        $action($request, $response, ['galleryId' => -1]);
    }
}
