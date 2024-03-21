<?php

namespace Jinya\Tests\Web\Actions\Gallery\Positions;

use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Gallery\Positions\UpdatePositionAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class UpdatePositionActionTest extends DatabaseAwareTestCase
{
    private GalleryFilePosition $position;

    public function test__invokeNoParameters(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdatePositionAction();
        $result = $action($request, $response, ['galleryId' => $this->position->galleryId, 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeNoFile(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdatePositionAction();
        $request = $request->withParsedBody(['newPosition' => 2]);
        $result = $action($request, $response, ['galleryId' => $this->position->galleryId, 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdatePositionAction();
        $request = $request->withParsedBody(['file' => -1]);
        $result = $action($request, $response, ['galleryId' => $this->position->galleryId, 'position' => 0]);
    }

    public function test__invokeNoPosition(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdatePositionAction();
        $request = $request->withParsedBody(['file' => $this->position->fileId]);
        $result = $action($request, $response, ['galleryId' => $this->position->galleryId, 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokePositionNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UpdatePositionAction();
        $result = $action($request, $response, ['galleryId' => -1, 'position' => -1]);
        self::assertEquals(204, $result->getStatusCode());
    }

    protected function setUp(): void
    {
        parent::setUp();
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $this->position = new GalleryFilePosition();
        $this->position->position = 0;
        $this->position->fileId = $file->getIdAsInt();
        $this->position->galleryId = $gallery->getIdAsInt();
        $this->position->create();
    }
}
