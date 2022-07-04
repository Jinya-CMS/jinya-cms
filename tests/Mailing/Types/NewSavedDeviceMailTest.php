<?php

namespace Jinya\Tests\Mailing\Types;

use App\Database\KnownDevice;
use App\Mailing\Types\NewSavedDeviceMail;
use Faker;
use PHPUnit\Framework\TestCase;

class NewSavedDeviceMailTest extends TestCase
{

    public function testSendMail(): void
    {
        $knownDevice = new KnownDevice();
        $knownDevice->userAgent = Faker\Factory::create()->firefox();
        $knownDevice->remoteAddress = '127.0.0.1';

        $savedDeviceMail = new NewSavedDeviceMail();
        $savedDeviceMail->sendMail('test@example.com', 'Theo Test', $knownDevice);
        self::assertTrue(true);
    }
}
