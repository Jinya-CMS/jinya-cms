<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 24.10.2017
 * Time: 18:08.
 */

namespace BackendBundle\Service\Users;

use BackendBundle\Form\AddUserData;
use BackendBundle\Form\UserData;
use DataBundle\Entity\User;

interface UserServiceInterface
{
    public function getUser(int $id): UserData;

    /**
     * @param int $offset
     * @param int $count
     *
     * @return UserData[]
     */
    public function getAllUsers(int $offset, int $count = 10): array;

    public function deleteUser(int $id);

    public function updateUser(int $id, UserData $userData): User;

    public function changePassword(int $id, string $newPassword);

    public function createUser(AddUserData $userData): User;

    public function activateUser(int $id);

    public function deactivateUser(int $id);

    public function grantRole(int $userId, string $role);

    public function revokeRole(int $userId, string $role);
}
