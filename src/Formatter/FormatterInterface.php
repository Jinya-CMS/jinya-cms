<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:38
 */

namespace Jinya\Formatter;

interface FormatterInterface
{
    /**
     * Formats the content of the @return array
     * @see FormatterInterface into an array
     *
     */
    public function format(): array;
}
