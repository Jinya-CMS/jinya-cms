<?php


namespace App\Database\Utils;


use Laminas\Hydrator\Filter\FilterInterface;

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

    /**
     * @inheritDoc
     */
    public function filter(string $property): bool
    {
        return !in_array($property, $this->propertiesToSkip, true);
    }
}