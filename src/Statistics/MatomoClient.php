<?php

namespace App\Statistics;

use App\OpenApiGeneration\Attributes\OpenApiResponse;
use JsonException;

class MatomoClient
{
    public const STATS_SCHEMA = [
        'label' => ['type' => 'string'],
        'visitCount' => ['type' => 'integer'],
    ];
    public const STATS_EXAMPLE = [
        'label' => OpenApiResponse::FAKER_WORD,
        'visitCount' => 32498,
    ];

    private string $matomoServer;
    private string $matomoApiKey;
    private string $matomoSiteId;

    private function __construct()
    {
    }

    public static function newClient(): MatomoClient
    {
        $client = new MatomoClient();
        $client->matomoApiKey = getenv('MATOMO_API_KEY');
        $client->matomoServer = getenv('MATOMO_SERVER');
        $client->matomoSiteId = getenv('MATOMO_SITE_ID');

        return $client;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByCountry(string $period = 'month', string $date = 'today'): array
    {
        $result = $this->requestMatomo('UserCountry.getCountry', $period, $date);

        $data = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $result
        );

        usort($data, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $data;
    }

    /**
     * @throws JsonException
     */
    private function requestMatomo(string $action, string $period = 'month', string $date = 'today'): array
    {
        $url = "https://matomo.statistical.li/?module=API&method=$action&idSite=$this->matomoSiteId&period=$period&date=$date&format=JSON&token_auth=$this->matomoApiKey&filter_sort_column=label&filter_sort_order=asc";
        $fetched = file_get_contents($url);

        return json_decode($fetched, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByBrowsers(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getBrowsers', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByOsVersions(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getOsVersions', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByDeviceBrand(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getBrand', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByDeviceType(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('DevicesDetection.getType', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByLanguage(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('UserLanguage.getLanguage', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }

    /**
     * @throws JsonException
     */
    public function getVisitsByReferrer(string $period = 'month', string $date = 'today'): array
    {
        $data = $this->requestMatomo('Referrers.getReferrerType', $period, $date);

        $result = array_map(
            static fn (array $item) => ['label' => $item['label'], 'visitCount' => $item['nb_visits']],
            $data
        );

        usort($result, static fn (array $item1, array $item2) => strcmp($item1['label'], $item2['label']));

        return $result;
    }
}
