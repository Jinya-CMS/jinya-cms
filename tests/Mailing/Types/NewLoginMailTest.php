<?php

namespace Jinya\Cms\Mailing\Types;

use Jinya\Cms\Database\ApiKey;
use Faker;
use PHPUnit\Framework\TestCase;

class NewLoginMailTest extends TestCase
{
    public function testSendMail(): void
    {
        $apiKey = new ApiKey();
        $apiKey->userAgent = Faker\Factory::create()->firefox();
        $apiKey->remoteAddress = '185.216.179.123';

        $loginMail = new NewLoginMail();
        $loginMail->sendMail('test@example.com', 'Theo Test', $apiKey);
        self::assertTrue(true);
    }
}
