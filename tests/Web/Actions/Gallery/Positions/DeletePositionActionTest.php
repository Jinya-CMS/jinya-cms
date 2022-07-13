<?php

namespace Jinya\Tests\Web\Actions\Gallery\Positions;

use App\Database\File;
use App\Database\Gallery;
use App\Database\GalleryFilePosition;
use App\Web\Actions\Gallery\Positions\DeletePositionAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class DeletePositionActionTest extends TestCase
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
        $action = new DeletePositionAction();
        $result = $action($request, $response, ['position' => 0, 'galleryId' => $gallery->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokePositionNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectDeprecationMessage('Gallery file position not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new DeletePositionAction();
        $result = $action($request, $response, ['position' => 0, 'galleryId' => -1]);
    }
}
