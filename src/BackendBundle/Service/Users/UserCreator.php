<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 22.10.2017
 * Time: 22:38
 */

namespace BackendBundle\Service\Users;


use BackendBundle\Entity\User;
use BackendBundle\Form\UserData;
use FOS\UserBundle\Doctrine\UserManager;

class UserCreator
{
    /** @var \FOS\UserBundle\Util\UserManipulator */
    private $userManager;

    /**
     * UserManipulator constructor.
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    public function create(UserData $userData)
    {
        /** @var User $user */
        $user = $this->userManager->createUser();
        $user->setUsername($userData->getUsername());
        $user->setEmail($userData->getEmail());
        $user->setPlainPassword($userData->getPassword());
        $user->setEnabled($userData->isActive());
        $user->setSuperAdmin($userData->isSuperAdmin());
        $user->setFirstname($userData->getFirstname());
        $user->setLastname($userData->getLastname());

        if ($userData->isWriter()) {
            $user->addRole(User::ROLE_WRITER);
        }
        if ($userData->isAdmin()) {
            $user->addRole(User::ROLE_ADMIN);
        }

        $this->userManager->updateUser($user);
        return $user;
    }
}