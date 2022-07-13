<?php

namespace Jinya\Tests\Web\Actions\Gallery\Positions;

use App\Database\File;
use App\Database\Gallery;
use App\Web\Actions\Gallery\Positions\CreatePositionAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class CreatePositionActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['position' => 0, 'file' => $file->getIdAsInt()]);
        $action = new CreatePositionAction();
        $result = $action($request, $response, ['galleryId' => $gallery->getIdAsInt()]);
        self::assertEquals(201, $result->getStatusCode());
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectDeprecationMessage('File not found');
        $gallery = new Gallery();
        $gallery->name = 'Foobar';
        $gallery->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['position' => 0, 'file' => -1]);
        $action = new CreatePositionAction();
        $action($request, $response, ['galleryId' => $gallery->getIdAsInt()]);
    }

    public function test__invokeGalleryNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectDeprecationMessage('Gallery not found');

        $file = new File();
        $file->name = 'Test';
        $file->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody(['position' => 0, 'file' => $file->getIdAsInt()]);
        $action = new CreatePositionAction();
        $action($request, $response, ['galleryId' => -1]);
    }
}
