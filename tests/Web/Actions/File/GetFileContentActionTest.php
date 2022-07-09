<?php

namespace Jinya\Tests\Web\Actions\File;

use App\Database\File;
use App\Storage\StorageBaseService;
use App\Web\Actions\File\GetFileContentAction;
use Faker\Provider\Uuid;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Slim\Exception\HttpNotFoundException;

class GetFileContentActionTest extends TestCase
{

    public function test__invoke(): void
    {
        $path = Uuid::uuid();
        $file = new File();
        $file->name = 'Test';
        $file->path = StorageBaseService::WEB_PATH . $path;
        $file->type = 'text/plain';
        $file->create();
        file_put_contents(StorageBaseService::SAVE_PATH . $path, 'Test');

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetFileContentAction();
        $result = $action($request, $response, ['id' => $file->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
        self::assertEquals($file->type, $result->getHeaderLine('Content-Type'));
        self::assertEquals('Test', $result->getBody()->getContents());

        unlink(StorageBaseService::SAVE_PATH . $path);
    }

    public function test__invokeFileNotFound(): void
    {
        $this->expectException(HttpNotFoundException::class);
        $this->expectExceptionMessage('File not found');
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetFileContentAction();
        $action($request, $response, ['id' => -1]);
    }

    public function test__invokeFileNoContentFound(): void
    {
        $file = new File();
        $file->name = 'Test';
        $file->create();

        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetFileContentAction();
        $result = $action($request, $response, ['id' => $file->getIdAsInt()]);
        $result->getBody()->rewind();

        self::assertEquals(200, $result->getStatusCode());
    }
}
