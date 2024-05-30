<?php

namespace Jinya\Cms\Console;

use Throwable;

#[JinyaCommand('migrate-configuration')]
class MigrateConfigurationCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->climate->info('Start migrating configuration');
        if (file_exists(__ROOT__ . '/.env') || file_exists(__ROOT__ . '/.env.dist')) {
            $config = $_ENV;
            if (empty($config)) {
                $this->climate->info('The .env or .env.dist file is empty');
                return;
            }

            try {
                $ini = <<<INI
[app]
env={$config['APP_ENV']}

[jinya]
api_key_expiry={$config['JINYA_API_KEY_EXPIRY']}
update_server={$config['JINYA_UPDATE_SERVER']}

[mailer]
encryption={$config['MAILER_ENCRYPTION']}
from={$config['MAILER_FROM']}
host={$config['MAILER_HOST']}
password={$config['MAILER_PASSWORD']}
port={$config['MAILER_PORT']}
smtp_auth={$config['MAILER_SMTP_AUTH']}
username={$config['MAILER_USERNAME']}

[mysql]
database={$config['MYSQL_DATABASE']}
host={$config['MYSQL_HOST']}
password={$config['MYSQL_PASSWORD']}
port={$config['MYSQL_PORT']}
user={$config['MYSQL_USER']}
INI;

                file_put_contents(__ROOT__.'/jinya-configuration.ini', $ini);
            } catch (Throwable) {
                $this->climate->error('Failed to save the ini variables');
            }
        } else {
            $this->climate->info('No .env or .env.dist file found');
        }
    }
}
