<?php

namespace Jinya\Services\Theme;

interface ThemeLinkServiceInterface
{
    /**
     * Returns the link config structure for the given theme
     *
     * @param string $themeName
     * @return array
     */
    public function getLinkConfigStructure(string $themeName): array;

    /**
     * Links the given page with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $pageSlug
     */
    public function savePage(string $key, string $themeName, string $pageSlug): void;

    /**
     * Links the given segment page with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $segmentPageSlug
     */
    public function saveSegmentPage(string $key, string $themeName, string $segmentPageSlug): void;

    /**
     * Links the given form with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $formSlug
     */
    public function saveForm(string $key, string $themeName, string $formSlug): void;

    /**
     * Links the given menu with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param int $menuId
     */
    public function saveMenu(string $key, string $themeName, int $menuId): void;

    /**
     * Links the given gallery with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param string $gallerySlug
     */
    public function saveGallery(string $key, string $themeName, string $gallerySlug): void;

    /**
     * Links the given file with the given theme
     *
     * @param string $key
     * @param string $themeName
     * @param int $fileId
     */
    public function saveFile(string $key, string $themeName, int $fileId): void;
}
