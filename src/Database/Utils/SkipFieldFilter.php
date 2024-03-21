<?php

namespace App\Database\Utils;

use JetBrains\PhpStorm\Pure;
use Laminas\Hydrator\Filter\FilterInterface;

/**
 * A simple laminas hydrator filter to skip properties during hydration and dehydration
 */
class SkipFieldFilter implements FilterInterface
{
    /** @var array<string> The properties to skip */
    private array $propertiesToSkip;

    /**
     * Creates a new instance of the SkipFieldFilter using the provided properties
     *
     * @param array<string> $propertiesToSkip The properties to skip during the filter stage
     */
    public function __construct(array $propertiesToSkip)
    {
        $this->propertiesToSkip = $propertiesToSkip;
    }

    /**
     * Returns true when the property is not available in the provided list of properties to skip
     *
     * @param string $property The property to filter
     * @param object|null $instance Not needed
     * @return bool
     */
    #[Pure] public function filter(string $property, ?object $instance = null): bool
    {
        return !in_array($property, $this->propertiesToSkip, true);
    }
}
