<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 02.11.18
 * Time: 00:19
 */

namespace Jinya\Framework\Profiling\Formatting;

use Symfony\Component\HttpKernel\Profiler\Profile;

interface ProfileFormatterInterface
{
    /**
     * Formats the given profile as array
     *
     * @param Profile $profile
     * @return array
     */
    public function format(Profile $profile): array;
}