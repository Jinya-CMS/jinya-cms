<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 31.08.18
 * Time: 09:43
 */

namespace Jinya\Framework\Events;

class Priority
{
    public const CRITICAL = 1000;
    public const HIGH = 800;
    public const MEDIUM = 600;
    public const LOW = 400;
    public const LOWEST = 200;
    public const LAST = 0;
}