<?php

namespace Jinya\Tests\Web\Exceptions;

use App\Web\Exceptions\MissingFieldsException;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class MissingFieldsExceptionTest extends TestCase
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
