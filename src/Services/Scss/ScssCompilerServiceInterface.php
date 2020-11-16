<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 11.01.2018
 * Time: 18:48
 */

namespace Jinya\Services\Scss;

interface ScssCompilerServiceInterface
{
    /**
     * Compiles the given source path into a css file
     */
    public function compileScss(string $code, string $importPath, array $variables): string;
}
