<?php

namespace Jinya\Cms\Mailing\Types;

use PHPUnit\Framework\TestCase;

class TwoFactorMailTest extends TestCase
{
    public function testSendMail(): void
    {
        $twoFactorMail = new TwoFactorMail();
        $twoFactorMail->sendMail('test@example.com', 'Theo Test', '123456');
        self::assertTrue(true);
    }
}
