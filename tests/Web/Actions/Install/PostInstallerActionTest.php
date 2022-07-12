<?php

namespace Jinya\Tests\Web\Actions\Install;

use App\Web\Actions\Install\PostInstallerAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class PostInstallerActionTest extends TestCase
{
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        @unlink(__ROOT__ . '/.env');
    }

    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new PostInstallerAction();
        $postBody = include __DIR__ . '/../../../files/.env.php';
        $request = $request->withParsedBody($postBody);
        $result = $action($request, $response, []);
        self::assertEquals(200, $result->getStatusCode());

        $request = new ServerRequest('', '');
        $response = new Response();
        $request = $request->withParsedBody([
                                                'email' => 'test@example.com',
                                                'password' => 'start1234',
                                                'artistname' => 'Theo Test',
                                                'action' => true,
                                            ]);
        $action = new PostInstallerAction();
        $result = $action($request, $response, []);
        self::assertEquals(301, $result->getStatusCode());
        self::assertEquals('/designer', $result->getHeaderLine('Location'));
    }

    public function test__invokeFirstAdminNoDotEnv(): void
    {
        $oldPort = getenv('MYSQL_PORT');
        try {
            putenv('MYSQL_PORT=0');
            $request = new ServerRequest('', '');
            $response = new Response();
            $request->withParsedBody([
                                         'email' => 'test@example.com',
                                         'password' => 'start1234',
                                         'artistname' => 'Theo Test',
                                     ]);
            $action = new PostInstallerAction();
            $result = $action($request, $response, []);
            self::assertEquals(200, $result->getStatusCode());
        } finally {
            putenv("MYSQL_PORT=$oldPort");
        }
    }
}
