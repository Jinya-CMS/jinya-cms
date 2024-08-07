<?php

namespace Jinya\Cms\Web\Controllers;

use Jinya\Cms\Analytics\EntityType;
use Jinya\Cms\Database\AnalyticsEntry;
use Jinya\Cms\Web\Middleware\AuthorizationMiddleware;
use Jinya\Router\Attributes\Controller;
use Jinya\Router\Attributes\HttpMethod;
use Jinya\Router\Attributes\Middlewares;
use Jinya\Router\Attributes\Route;
use Jinya\Router\Extensions\Database\Exceptions\NotFoundException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

#[Controller]
class StatisticsController extends BaseController
{
    /**
     * @throws NotFoundException
     * @throws JsonException
     */
    #[Route(HttpMethod::GET, 'api/statistics/{group:country|os|browser|os-version|browser-version|brand|type|language|date}/{interval:[1-9]\d*}/{type:month|year}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getStatisticsGrouped(
        string $group,
        int $interval,
        string $type
    ): ResponseInterface {
        if (!in_array($type, ['year', 'month'], true)) {
            throw new NotFoundException($this->request);
        }

        if ($type === 'year') {
            $interval *= 12;
        }

        $data = AnalyticsEntry::getPastIntervalGrouped(
            $group,
            "$interval month",
            uniqueOnly: true,
        );

        return $this->json($data);
    }

    #[Route(HttpMethod::GET, 'api/statistics/visits/{interval:[1-9]\d*}/{type:month|year}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getTotalVisitsRanged(int $interval, string $type): ResponseInterface
    {
        if (!in_array($type, ['year', 'month'], true)) {
            throw new NotFoundException($this->request);
        }

        if ($type === 'year') {
            $interval *= 12;
        }

        return $this->json(AnalyticsEntry::getTotalPastInterval("$interval month"));
    }

    #[Route(HttpMethod::GET, 'api/statistics/visits/{interval:[1-9]\d*}/{type:month|year}/{entityType:blog-post|blog-category|form|gallery|modern-page|classic-page|artist}/{entityId:\d*}')]
    #[Middlewares(new AuthorizationMiddleware(ROLE_READER))]
    public function getLastMonthEntityType(
        int $interval,
        string $type,
        string $entityType,
        int $entityId
    ): ResponseInterface {
        if (!in_array(
            $entityType,
            ['blog-post', 'blog-category', 'form', 'gallery', 'modern-page', 'classic-page', 'artist'],
            true
        )) {
            throw new NotFoundException($this->request);
        }

        if (!in_array($type, ['year', 'month'], true)) {
            throw new NotFoundException($this->request);
        }

        if ($type === 'year') {
            $interval *= 12;
        }

        return $this->json(
            AnalyticsEntry::getPastNMonthsForEntity($interval, EntityType::fromString($entityType), $entityId)
        );
    }
}
