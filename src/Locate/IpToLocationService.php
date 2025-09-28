<?php

namespace Jinya\Cms\Locate;

use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Cms\Configuration\JinyaConfiguration;
use Jinya\Cms\Logging\Logger;
use Jinya\Database\Entity;
use Psr\Log\LoggerInterface;
use Throwable;

readonly class IpToLocationService
{
    private LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    public function populateDatabase(): bool
    {
        $databaseUrl = (string)JinyaConfiguration::getConfiguration()->get('ip_database_url', 'jinya', 'https://download.db-ip.com/free/dbip-city-lite-{YEAR}-{MONTH}.csv.gz');
        $yesterday = new DateTime();
        $yesterday->modify('-1 day');
        $databaseUrl = str_replace(array('{YEAR}', '{MONTH}'), array($yesterday->format('Y'), $yesterday->format('m')), $databaseUrl);

        $database = gzopen($databaseUrl, 'rb');
        if ($database) {
            try {
                Entity::getPDO()->beginTransaction();

                $query = Entity::getQueryBuilder()
                    ->newDelete()
                    ->from('ip_address');

                Entity::executeQuery($query);

                $statement = Entity::getPDO()->prepare(
                    'INSERT INTO ip_address (address_type, ip_start, ip_end, country, city) VALUES (:type, :ipFrom, :ipTo, :countryCode, :city)'
                );
                $batchSize = 1000;
                $counter = 0;

                while (($line = fgetcsv($database, 1024, ',', '"', "\0")) !== false) {
                    [$ipFrom, $ipTo, , $countryCode, , $city] = $line;

                    $type = match (ip2long($ipFrom)) {
                        false => 'ipv6',
                        default => 'ipv4'
                    };

                    $statement->bindValue(':type', $type);
                    $statement->bindValue(':ipFrom', inet_pton($ipFrom));
                    $statement->bindValue(':ipTo', inet_pton($ipTo));
                    $statement->bindValue(':countryCode', $countryCode);
                    $statement->bindValue(':city', $city);
                    $statement->execute();

                    $counter++;

                    if ($counter % $batchSize === 0) {
                        Entity::getPDO()->commit();
                        Entity::getPDO()->beginTransaction();
                    }
                }

                fclose($database);

                Entity::getPDO()->commit();

                return true;
            } catch (Throwable $e) {
                Entity::getPDO()->rollBack();
                $this->logger->error($e->getMessage());
            }
        }

        return false;
    }

    /**
     * @param string $ip
     * @return array{'city': string, 'country': string}
     */
    #[ArrayShape([
        'city' => 'string',
        'country' => 'string',
    ])]
    public function locateIp(string $ip): array
    {
        $isV6 = ip2long($ip) === false;
        $pton = inet_pton($ip);
        if ($pton !== false) {
            $ipAddress = bin2hex($pton);
            $query = Entity::getQueryBuilder()
                ->newSelect()
                ->cols(['country', 'city'])
                ->from('ip_address')
                ->where('ip_start <= UNHEX(?) && UNHEX(?) <= ip_end', [$ipAddress, $ipAddress])
                ->limit(1);

            if ($isV6) {
                $query = $query->where("address_type = 'ipv6'");
            } else {
                $query = $query->where("address_type = 'ipv4'");
            }

            /** @var array{'city': string, 'country': string}[] $result */
            $result = Entity::executeQuery($query);

            if (!empty($result)) {
                return $result[0];
            }
        }

        return ['country' => '-1','city' => '-1'];
    }
}
