<?php

namespace Jinya\Tests\Web\Actions\File\Upload;

use App\Database\File;
use App\Web\Actions\File\Upload\FinishUploadAction;
use App\Web\Actions\File\Upload\StartUploadAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpNotFoundException;

class FinishUploadActionTest extends TestCase
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

        $finish = new FinishUploadAction();
        $result = $finish($request, $response, ['id' => $file->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('File not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new FinishUploadAction();
        $action($request, $response, ['id' => -1]);
    }
}
