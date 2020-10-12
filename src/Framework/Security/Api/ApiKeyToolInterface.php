<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 10.03.2018
 * Time: 22:41
 */

namespace Jinya\Framework\Security\Api;

use Jinya\Entity\Artist\User;

interface ApiKeyToolInterface
{
    /**
     * Creates an api key for the given user
     */
    public function createApiKey(User $user): string;

    /**
     * Gets the user for the given api key
     */
    public function getUserByKey(string $key): User;

    /**
     * Check if the given key exists in the database
     */
    public function keyExists(string $key): bool;

    /**
     * Invalidates the given api key
     */
    public function invalidate(string $key): void;

    /**
     * Refreshes the validate since time
     */
    public function refreshToken(string $key): void;

    /**
     * Checks whether the given api key should get invalidated
     */
    public function shouldInvalidate(string $key): bool;

    /**
     * Invalidates all tokens for the given user
     */
    public function invalidateAll(int $userId): void;

    /**
     * Invalidates the given api key if it is owned by the given user
     */
    public function invalidateKeyOfUser(string $username, string $key): void;

    /**
     * Gets all api keys for the given user
     */
    public function getAllForUser(string $email): array;
}
