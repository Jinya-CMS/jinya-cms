<?php

namespace Jinya\Cms\Console;

use Jinya\Cms\Locate\IpToLocationService;

#[JinyaCommand('populate-ip-database')]
class PopulateIpDatabaseCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->climate->info('Populating the ip database...');
        $ipToLocationService = new IpToLocationService();
        if (!$ipToLocationService->populateDatabase()) {
            $this->climate->error('Failed to populate ip database.');
            return;
        }

        $ip = '8.8.8.8';
        $location = $ipToLocationService->locateIp($ip);
        $this->climate->info("Location for IP {$ip}: {$location['country']} {$location['city']}");

        $this->climate->info('Populated the ip database');
    }
}
