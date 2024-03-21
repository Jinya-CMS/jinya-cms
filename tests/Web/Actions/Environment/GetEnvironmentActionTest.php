<?php

namespace Jinya\Tests\Web\Actions\Environment;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Actions\Environment\GetEnvironmentAction;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

class GetEnvironmentActionTest extends DatabaseAwareTestCase
{
    public function test__invoke(): void
    {
        $request = new ServerRequest('', '');
        $response = new Response();
        $action = new GetEnvironmentAction();
        $result = $action($request, $response, []);
        $result->getBody()->rewind();
        $body = json_decode($result->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        foreach ($body as $item) {
            if (stripos($item['key'], 'password')) {
                self::assertEquals('••••••', $item['value']);
            }
        }
    }
}
