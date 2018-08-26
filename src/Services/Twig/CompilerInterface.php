<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:21
 */

namespace Jinya\Services\Twig;

interface CompilerInterface
{
    /**
     * Compiles the given template with the given context
     *
     * @param string $path
     * @param array $context
     * @return string
     */
    public function compile(string $path, array $context): string;
}
