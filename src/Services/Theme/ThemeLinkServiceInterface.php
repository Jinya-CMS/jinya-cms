<?php

namespace Jinya\Services\Theme;

interface ThemeLinkServiceInterface
{
    /**
     * Returns the link config structure for the given theme
     */
    public function getLinkConfigStructure(string $themeName): array;

    /**
     * Links the given page with the given theme
     */
    public function savePage(string $key, string $themeName, string $pageSlug): void;

    /**
     * Links the given segment page with the given theme
     */
    public function saveSegmentPage(string $key, string $themeName, string $segmentPageSlug): void;

    /**
     * Links the given form with the given theme
     */
    public function saveForm(string $key, string $themeName, string $formSlug): void;

    /**
     * Links the given menu with the given theme
     */
    public function saveMenu(string $key, string $themeName, int $menuId): void;

    /**
     * Links the given gallery with the given theme
     */
    public function saveGallery(string $key, string $themeName, string $gallerySlug): void;

    /**
     * Links the given file with the given theme
     */
    public function saveFile(string $key, string $themeName, int $fileId): void;
}
