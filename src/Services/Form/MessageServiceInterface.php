<?php

namespace Jinya\Services\Form;

use Jinya\Entity\Form\Message;

interface MessageServiceInterface
{
    /**
     * Gets the specified
     * @return Message @see Message by slug
     */
    public function get(int $id): Message;

    /**
     * Gets all entities by the given parameters
     *
     * @return Message[]
     */
    public function getAll(
        int $offset = 0,
        int $count = 10,
        string $keyword = '',
        string $formSlug = '',
        string $action = ''
    ): array;

    /**
     * Counts all entities
     */
    public function countAll(string $keyword = '', string $formId = '', string $action = ''): int;

    /**
     * Saves or updates the given @param Message $message
     * @see Message
     */
    public function saveOrUpdate(Message $message): Message;

    /**
     * Deletes the given @param Message $message
     * @see Message
     */
    public function delete(Message $message): void;
}
