<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:36
 */

namespace Jinya\Services\Slug;

interface SlugServiceInterface
{
    /**
     * Generates a slug from the given name
     */
    public function generateSlug(string $name): string;
}
