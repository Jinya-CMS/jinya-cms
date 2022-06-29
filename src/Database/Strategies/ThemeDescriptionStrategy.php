<?php

namespace App\Database\Strategies;

use JsonException;
use Laminas\Hydrator\Strategy\StrategyInterface;

/**
 * Laminas hydrator strategy to serialize and deserialize theme descriptions
 */
class ThemeDescriptionStrategy implements StrategyInterface
{
    /**
     * @inheritDoc
     * @throws JsonException
     */
    public function extract($value, ?object $object = null)
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * @inheritDoc
     * @param array<string>|null $data
     * @throws JsonException
     */
    public function hydrate($value, ?array $data)
    {
        try {
            return json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return ['en' => $value];
        }
    }
}
