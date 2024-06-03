<?php

namespace Jinya\Cms\Analytics;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use Jinya\Cms\Database\AnalyticsEntry;
use Jinya\Cms\Database\BlogPost;
use Jinya\Cms\Database\MenuItem;
use Jinya\Cms\Logging\Logger;
use Jinya\Cms\Web\Controllers\BaseController;
use League\Uri\Uri;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AnalyticsService
{
    private readonly LoggerInterface $logger;

    private readonly DeviceDetector $detector;

    private readonly ServerRequestInterface $request;

    public function __construct()
    {
        $this->request = get_request();
        $this->logger = Logger::getLogger();
        $this->detector = new DeviceDetector(
            $this->request->getHeaderLine('User-Agent'),
            ClientHints::factory($_SERVER)
        );
        $this->detector->discardBotInformation();
        $this->detector->parse();
    }

    public function trackRequest(): ?AnalyticsEntry
    {
        try {
            if ($this->detector->isBot()) {
                $this->logger->info('The request is made by a robot, ignore it');
                return null;
            }

            $this->logger->info("Tracking request for URL {$this->request->getUri()}");

            $entry = new AnalyticsEntry();
            if ($this->detector->isTablet()) {
                $entry->deviceType = DeviceType::Tablet;
            } elseif ($this->detector->isMobile()) {
                $entry->deviceType = DeviceType::Smartphone;
            } elseif ($this->detector->isDesktop()) {
                $entry->deviceType = DeviceType::Computer;
            } else {
                $entry->deviceType = DeviceType::Other;
            }

            $entry->userAgent = $this->detector->getUserAgent();
            $entry->device = $this->detector->getModel();
            $entry->brand = $this->detector->getBrandName();

            /** @var array<string, string> $client */
            $client = $this->detector->getClient();

            $entry->browser = $client['name'];
            $entry->browserVersion = $client['version'];

            /** @var array<string, string> $os */
            $os = $this->detector->getOs();

            $entry->operatingSystem = $os['name'];
            $entry->operatingSystemVersion = $os['version'];

            $entry->route = substr($this->request->getUri()->getPath(), 1);

            $entry->language = substr($this->request->getHeaderLine('Accept-Language'), 0, 2);

            $referer = Uri::new($this->request->getHeaderLine('Referer'));
            $host = $this->request->getUri()->getHost();

            $entry->uniqueVisit = $referer->getHost() !== $host;

            $entry->referer = $referer->getHost();

            $entry->create();

            return $entry;
        } catch (Throwable $throwable) {
            $this->logger->warning('Failed to track this request');
            $this->logger->warning($throwable);

            return null;
        }
    }

    public function trackMenuItem(MenuItem $menuItem): ?AnalyticsEntry
    {
        $entry = $this->trackRequest();
        if (!$entry) {
            return null;
        }

        if ($menuItem->artistId) {
            $entry->entityId = $menuItem->artistId;
            $entry->entityType = EntityType::Artist;
        } elseif ($menuItem->formId) {
            $entry->entityId = $menuItem->formId;
            $entry->entityType = EntityType::Artist;
        } elseif ($menuItem->categoryId) {
            $entry->entityId = $menuItem->categoryId;
            $entry->entityType = EntityType::BlogCategory;
        } elseif ($menuItem->classicPageId) {
            $entry->entityId = $menuItem->classicPageId;
            $entry->entityType = EntityType::ClassicPage;
        } elseif ($menuItem->modernPageId) {
            $entry->entityId = $menuItem->modernPageId;
            $entry->entityType = EntityType::ModernPage;
        } elseif ($menuItem->galleryId) {
            $entry->entityId = $menuItem->galleryId;
            $entry->entityType = EntityType::Gallery;
        }

        $entry->update();

        return $entry;
    }

    public function trackBlogPost(BlogPost $post): ?AnalyticsEntry
    {
        $entry = $this->trackRequest();
        if (!$entry) {
            return null;
        }

        $entry->entityId = $post->id;
        $entry->entityType = EntityType::BlogPost;

        $entry->update();

        return $entry;
    }

    public function trackNotFound(): ?AnalyticsEntry
    {
        $entry = $this->trackRequest();
        if (!$entry) {
            return null;
        }

        $entry->status = BaseController::HTTP_NOT_FOUND;

        $entry->update();

        return $entry;
    }
}
