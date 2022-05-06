<?php

namespace App\Database\Utils;

use JetBrains\PhpStorm\Pure;
use Laminas\Hydrator\Filter\FilterInterface;

/**
 *
 */
class SkipFieldFilter implements FilterInterface
{

    private array $propertiesToSkip;

    /**
     * SkipFieldFilter constructor.
     * @param array $propertiesToSkip
     */
    public function __construct(array $propertiesToSkip)
    {
        $this->propertiesToSkip = $propertiesToSkip;
    }

    #[Pure] public function filter(string $property, ?object $instance = null): bool
    {
        return !in_array($property, $this->propertiesToSkip, true);
    }
}