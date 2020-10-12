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
     */
    public function compile(string $path, array $context): string;

    /**
     * Adds a global variable to template engine
     *
     * @param $value
     */
    public function addGlobal(string $name, $value): void;
}
