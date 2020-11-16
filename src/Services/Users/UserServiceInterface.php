<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08.
 */

namespace Jinya\Services\Users;

use Jinya\Entity\Artist\User;

interface UserServiceInterface
{
    /**
     * Gets the user specified by the id
     */
    public function get(int $id): User;

    /**
     * Gets all users in the given range
     *
     * @return User[]
     */
    public function getAll(string $keyword = ''): array;

    /**
     * Counts all users
     */
    public function countAll(string $keyword): int;

    /**
     * Deletes the given user
     */
    public function delete(int $id): void;

    /**
     * Changes the password for the given user
     */
    public function changePassword(int $id, string $newPassword): void;

    /**
     * Creates a user
     */
    public function saveOrUpdate(User $user, bool $ignorePassword = false): User;

    /**
     * Activates the given user
     */
    public function activate(int $id): User;

    /**
     * Deactivates the given user
     */
    public function deactivate(int $id): User;

    /**
     * Grants the given role for the given user
     */
    public function grantRole(int $userId, string $role): void;

    /**
     * Revokes the given role for the given user
     */
    public function revokeRole(int $userId, string $role): void;

    /**
     * Gets the user by username and password
     */
    public function getUser(string $username, string $password, string $twoFactorCode, string $deviceCode): User;

    /**
     * Gets the user with the given email address
     */
    public function getUserByEmail(string $email): User;
}
