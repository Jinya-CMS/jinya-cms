<?php

namespace Jinya\Tests\Mailing\Types;

use App\Database\ApiKey;
use App\Mailing\Types\NewLoginMail;
use Faker;
use PHPUnit\Framework\TestCase;

class NewLoginMailTest extends TestCase
{

    public function testSendMail(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = Faker\Factory::create()->firefox();
        $apiKey->remoteAddress = '127.0.0.1';

        $loginMail = new NewLoginMail();
        $loginMail->sendMail('test@example.com', 'Theo Test', $apiKey);
        self::assertTrue(true);
    }
}
