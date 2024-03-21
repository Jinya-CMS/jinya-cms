<?php

namespace App\Statistics;

use DateInterval;
use DateTime;
use JsonException;

/**
 * The matomo client connects to the configured matomo instance and offers a few statistics methods
 * @codeCoverageIgnore
 */
class MatomoClient
{
    /** @var string The matomo server to query */
    private string $matomoServer;
    /** @var string The api key to use to connect to the matomo server */
    private string $matomoApiKey;
    /** @var string The ID of the site you are serving from */
    private string $matomoSiteId;

    /** @internal */
    private function __construct()
    {
    }

    /**
     * Creates a new matomo client with the ENV configured values
     *
     * @return MatomoClient
     */
    public static function newClient(): MatomoClient
    {
        $client = new MatomoClient();
        $client->matomoApiKey = getenv('MATOMO_API_KEY') ?: '';
        $client->matomoServer = getenv('MATOMO_SERVER') ?: '';
        $client->matomoSiteId = getenv('MATOMO_SITE_ID') ?: '';

        return $client;
    }

    /**
     * Gets the visits by country in the given period
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByCountry(string $period = 'range'): array
    {
        $result = $this->requestMatomo('UserCountry.getCountry', $period);

        $data = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $result
        );

        usort($data, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $data;
    }

    /**
     * Sends a request to matomo
     *
     * @param string $action The action execute
     * @param string $period The period to request
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    private function requestMatomo(string $action, string $period = 'range'): array
    {
        $today = new DateTime();
        $to = $today->format('Y-m-d');
        $from = $today->sub(new DateInterval('P1M'))->format('Y-m-d');
        $date = "$from,$to";
        $url = "$this->matomoServer?module=API&method=$action&idSite=$this->matomoSiteId&period=$period&date=$date&format=JSON&token_auth=$this->matomoApiKey&filter_sort_column=label&filter_sort_order=asc";
        $fetched = file_get_contents($url) ?: '';

        return json_decode($fetched, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Gets the visits by browser for the given period
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByBrowsers(string $period = 'range'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getBrowsers', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }

    /**
     * Gets the visits by os version
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByOsVersions(string $period = 'range'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getOsVersions', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }

    /**
     * Gets the visits by device brand
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByDeviceBrand(string $period = 'range'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getBrand', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }

    /**
     * Gets the visits by device type
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByDeviceType(string $period = 'range'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getType', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }

    /**
     * Gets the visits by language
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByLanguage(string $period = 'range'): array
    {
        $data = $this->requestMatomo('UserLanguage.getLanguage', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }

    /**
     * Gets the visits by referrer
     *
     * @param string $period The period to look for
     * @return array<array<string, mixed>>
     * @throws JsonException
     */
    public function getVisitsByReferrer(string $period = 'range'): array
    {
        $data = $this->requestMatomo('Referrers.getReferrerType', $period);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));
        return $result;
    }
}
