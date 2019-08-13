<?php

namespace Jinya\Services\Form;

use Jinya\Entity\Form\Message;

interface MessageServiceInterface
{
    /**
     * Gets the specified @param string $slug
     * @return Message
     * @see Message by slug
     */
    public function get(string $slug): Message;

    /**
     * Gets all entities by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @param int $formId
     * @return Message[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = '', int $formId = -1): array;

    /**
     * Counts all entities
     *
     * @param string $keyword
     * @param int $formId
     * @return int
     */
    public function countAll(string $keyword = '', int $formId = -1): int;

    /**
     * Saves or updates the given @param Message $message
     * @return Message
     * @see Message
     */
    public function saveOrUpdate(Message $message): Message;

    /**
     * Deletes the given @param Message $message
     * @see Message
     */
    public function delete(Message $message): void;
}
