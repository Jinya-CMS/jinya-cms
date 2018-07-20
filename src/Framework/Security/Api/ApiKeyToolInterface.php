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
     *
     * @param \Jinya\Entity\Artist\User $user
     * @return string
     */
    public function createApiKey(User $user): string;

    /**
     * Gets the user for the given api key
     *
     * @param string $key
     * @return \Jinya\Entity\Artist\User
     */
    public function getUserByKey(string $key): User;

    /**
     * Invalidates the given api key
     *
     * @param string $key
     */
    public function invalidate(string $key): void;

    /**
     * Refreshes the validate since time
     *
     * @param string $key
     */
    public function refreshToken(string $key): void;

    /**
     * Checks whether the given api key should get invalidated
     *
     * @param string $key
     * @return bool
     */
    public function shouldInvalidate(string $key): bool;

    /**
     * Invalidates all tokens for the given user
     *
     * @param int $userId
     */
    public function invalidateAll(int $userId): void;

    /**
     * Invalidates the given api key if it is owned by the given user
     *
     * @param string $username
     * @param string $key
     */
    public function invalidateKeyOfUser(string $username, string $key): void;

    /**
     * Gets all api keys for the given user
     *
     * @param string $email
     * @return array
     */
    public function getAllForUser(string $email): array;
}
