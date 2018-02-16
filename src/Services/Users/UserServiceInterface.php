<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08.
 */

namespace Jinya\Services\Users;

use Jinya\Entity\User;
use Jinya\Form\Backend\AddUserData;
use Jinya\Form\Backend\UserData;

interface UserServiceInterface
{
    /**
     * Gets the user specified by the id
     *
     * @param int $id
     * @return UserData
     */
    public function getUser(int $id): UserData;

    /**
     * Gets all users in the given range
     *
     * @param int $offset
     * @param int $count
     *
     * @return UserData[]
     */
    public function getAllUsers(int $offset, int $count = 10): array;

    /**
     * Deletes the given user
     *
     * @param int $id
     */
    public function deleteUser(int $id): void;

    /**
     * Updates the given user
     *
     * @param int $id
     * @param UserData $userData
     * @return User
     */
    public function updateUser(int $id, UserData $userData): User;

    /**
     * Changes the password for the given user
     *
     * @param int $id
     * @param string $newPassword
     */
    public function changePassword(int $id, string $newPassword): void;

    /**
     * Creates a user
     *
     * @param AddUserData $userData
     * @return User
     */
    public function createUser(AddUserData $userData): User;

    /**
     * Activates the given user
     *
     * @param int $id
     * @return User
     */
    public function activateUser(int $id): User;

    /**
     * Deactivates the given user
     *
     * @param int $id
     * @return User
     */
    public function deactivateUser(int $id): User;

    /**
     * Grants the given role for the given user
     *
     * @param int $userId
     * @param string $role
     */
    public function grantRole(int $userId, string $role): void;

    /**
     * Revokes the given role for the given user
     *
     * @param int $userId
     * @param string $role
     */
    public function revokeRole(int $userId, string $role): void;
}
