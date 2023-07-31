<?php

namespace Jinya\Tests\Mailing\Factory;

use App\Mailing\Factory\MailerFactory;
use PHPUnit\Framework\TestCase;

class MailerFactoryTest extends TestCase
{

    public function testGetMailer(): void
    {
        $mailer = MailerFactory::getMailer();
        self::assertNotNull($mailer);
        if (getenv('EXECUTE_IN') === 'ci') {
            self::assertEquals('127.0.0.1', $mailer->Host);
        } else {
            self::assertEquals('jinya-mailhog', $mailer->Host);
        }
        self::assertEquals(1025, $mailer->Port);
        self::assertEquals('test@example.com', $mailer->Username);
        self::assertEquals('start1234', $mailer->Password);
        self::assertEquals('tls', $mailer->SMTPSecure);
        self::assertEquals(true, $mailer->SMTPAuth);
    }
}
