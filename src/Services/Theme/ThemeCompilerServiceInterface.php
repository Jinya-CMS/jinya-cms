<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 07:52
 */

namespace Jinya\Services\Theme;


use Jinya\Entity\Theme;

interface ThemeCompilerServiceInterface
{
    /**
     * Compiles the scss and javascript of the given @see Theme
     *
     * @param Theme $theme
     */
    public function compileTheme(Theme $theme): void;

    /**
     * Checks whether the given theme is compiled
     *
     * @param Theme $theme
     * @return bool
     */
    public function isCompiled(Theme $theme): bool;
}