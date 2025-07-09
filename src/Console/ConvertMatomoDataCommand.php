<?php

namespace Jinya\Cms\Console;

use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use DeviceDetector\Parser\OperatingSystem;
use Exception;
use Jinya\Cms\Analytics\EntityType;
use Jinya\Cms\Database\AnalyticsEntry;
use Jinya\Cms\Database\BlogPost;
use Jinya\Cms\Database\MenuItem;

#[JinyaCommand('convert-matomo')]
class ConvertMatomoDataCommand extends AbstractCommand
{
    public function run(): void
    {
        $this->climate->info('Start converting matomo data');
        /** @var AnalyticsEntry[] $entries */
        $entries = iterator_to_array(AnalyticsEntry::findByFilters(['user_agent is null' => []]));

        $progress = $this->climate->progress()->total(count($entries));

        foreach ($entries as $entry) {
            $progress->advance(1, "#$entry->id: {$entry->timestamp->format('Y-m-d')} $entry->route");

            $entry->language = strtolower($entry->language) ?: $entry->language;
            $entry->browser = Browser::getAvailableBrowsers()[$entry->browser] ?? $entry->browser;
            $entry->operatingSystem = OperatingSystem::getNameFromId(
                $entry->operatingSystem
            ) ?? $entry->operatingSystem;
            $entry->brand = AbstractDeviceParser::getFullName($entry->brand) ?: $entry->brand;

            $menuItem = MenuItem::findByRoute($entry->route);
            if ($menuItem?->galleryId) {
                $entry->entityId = $menuItem->galleryId;
                $entry->entityType = EntityType::Gallery;
            } elseif ($menuItem?->formId) {
                $entry->entityId = $menuItem->formId;
                $entry->entityType = EntityType::Form;
            } elseif ($menuItem?->classicPageId) {
                $entry->entityId = $menuItem->classicPageId;
                $entry->entityType = EntityType::ClassicPage;
            } elseif ($menuItem?->modernPageId) {
                $entry->entityId = $menuItem->modernPageId;
                $entry->entityType = EntityType::ModernPage;
            } elseif ($menuItem?->categoryId) {
                $entry->entityId = $menuItem->categoryId;
                $entry->entityType = EntityType::BlogCategory;
            } elseif ($menuItem?->artistId) {
                $entry->entityId = $menuItem->artistId;
                $entry->entityType = EntityType::Artist;
            } elseif (!$menuItem?->blogHomePage) {
                $postSlug = substr($entry->route, 11);
                $post = BlogPost::findBySlug($postSlug);
                if ($post) {
                    $entry->entityId = $post->id;
                    $entry->entityType = EntityType::BlogPost;
                }
            }

            try {
                $entry->update();
            } catch (Exception $exception) {
                $this->climate->error('Failed to save entry');
                $this->climate->error($exception->getMessage());
            }
        }
    }
}
