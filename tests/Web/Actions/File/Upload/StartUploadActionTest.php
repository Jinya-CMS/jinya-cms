<?php

namespace Jinya\Tests\Web\Actions\File\Upload;

use App\Database\File;
use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\File\Upload\StartUploadAction;
use App\Web\Exceptions\ConflictException;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use Slim\Exception\HttpNotFoundException;

class StartUploadActionTest extends DatabaseAwareTestCase
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
    }

    public function test__invokeDuplicateCreation(): void
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('Upload started already');

        $file = new File();
        $file->name = 'Test';
        $file->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new StartUploadAction();
        $result = $action($request, $response, ['id' => $file->getIdAsInt()]);
        self::assertEquals(204, $result->getStatusCode());

        $action($request, $response, ['id' => $file->getIdAsInt()]);
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('File not found');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new StartUploadAction();
        $action($request, $response, ['id' => -1]);
    }
}
