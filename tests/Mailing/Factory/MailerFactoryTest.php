<?php

namespace Jinya\Cms\Mailing\Factory;

use Jinya\Cms\Configuration\JinyaConfiguration;
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
        self::assertEquals(JinyaConfiguration::getConfiguration()->get("port", "mailer"), $mailer->Port);
        self::assertEquals(JinyaConfiguration::getConfiguration()->get("username", "mailer"), $mailer->Username);
        self::assertEquals(JinyaConfiguration::getConfiguration()->get("password", "mailer"), $mailer->Password);
        self::assertEquals(JinyaConfiguration::getConfiguration()->get("encryption", "mailer"), $mailer->SMTPSecure);
        self::assertEquals(JinyaConfiguration::getConfiguration()->get("smtp_auth", "mailer"), $mailer->SMTPAuth);
    }
}
