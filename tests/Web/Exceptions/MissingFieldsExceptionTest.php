<?php

namespace Jinya\Tests\Web\Exceptions;

use App\Tests\DatabaseAwareTestCase;
use App\Web\Exceptions\MissingFieldsException;
use Nyholm\Psr7\ServerRequest;

class MissingFieldsExceptionTest extends DatabaseAwareTestCase
{
    public function testJsonSerialize(): void
    {
        $request = new ServerRequest('', '');
        $exception = new MissingFieldsException($request, ['test']);

        $data = $exception->jsonSerialize();
        self::assertArrayHasKey('success', $data);
        self::assertArrayHasKey('fields', $data);
    }
}
