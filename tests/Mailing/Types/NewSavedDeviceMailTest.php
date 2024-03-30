<?php

namespace Jinya\Cms\Mailing\Types;

use Jinya\Cms\Database\KnownDevice;
use Faker;
use PHPUnit\Framework\TestCase;

class NewSavedDeviceMailTest extends TestCase
{
    public function testSendMail(): void
    {
        $knownDevice = new KnownDevice();
        $knownDevice->userAgent = Faker\Factory::create()->firefox();
        $knownDevice->remoteAddress = '185.216.179.123';

        $savedDeviceMail = new NewSavedDeviceMail();
        $savedDeviceMail->sendMail('test@example.com', 'Theo Test', $knownDevice);
        self::assertTrue(true);
    }
}
