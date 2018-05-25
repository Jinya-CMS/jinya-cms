<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 18:56
 */

namespace Jinya\Services\Theme;

interface ThemeSyncServiceInterface
{
    /**
     * Synchronizes the themes from the filesystem
     */
    public function syncThemes(): void;
}
