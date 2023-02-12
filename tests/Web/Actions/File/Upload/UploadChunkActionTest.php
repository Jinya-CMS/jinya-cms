<?php

namespace Jinya\Tests\Web\Actions\File\Upload;

use App\Database\File;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\File\Upload\StartUploadAction;
use App\Web\Actions\File\Upload\UploadChunkAction;
use App\Web\Exceptions\NoResultException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Nyholm\Psr7\Stream;

class UploadChunkActionTest extends DatabaseAwareTestCase
{

    public function test__invoke(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new StartUploadAction();
        $result = $action($request, $response, ['id' => $file->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());

        $request = $request->withBody(Stream::create('Test'));
        $request->getBody()->rewind();
        $chunk = new UploadChunkAction();
        $result = $chunk($request, $response, ['id' => $file->getIdAsInt(), 'position' => 0]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(NoResultException::class);
        $this->expectExceptionMessage('File not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new UploadChunkAction();
        $action($request, $response, ['id' => -1, 'position' => 0]);
    }
}
