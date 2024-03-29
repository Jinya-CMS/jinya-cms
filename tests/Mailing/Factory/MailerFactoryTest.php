<?php

namespace Jinya\Tests\Mailing\Factory;

use App\Mailing\Factory\MailerFactory;
use PHPUnit\Framework\TestCase;

class MailerFactoryTest extends TestCase
{
    public function testGetMailer(): void
    {
        if (getenv('EXECUTE_IN') === 'ci') {
            putenv('MAILER_HOST=mailer');
            $mailer = MailerFactory::getMailer();
            self::assertEquals('mailer', $mailer->Host);
        } else {
            putenv('MAILER_HOST=jinya-mailer');
            $mailer = MailerFactory::getMailer();
            self::assertEquals('jinya-mailer', $mailer->Host);
        }
        self::assertNotNull($mailer);
        self::assertEquals(1025, $mailer->Port);
        self::assertEquals('test@example.com', $mailer->Username);
        self::assertEquals('start1234', $mailer->Password);
        self::assertEquals('tls', $mailer->SMTPSecure);
        self::assertTrue($mailer->SMTPAuth);
    }
}
