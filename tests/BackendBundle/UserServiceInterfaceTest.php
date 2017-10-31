<?php


use BackendBundle\Entity\User;
use BackendBundle\Form\AddUserData;
use BackendBundle\Form\UserData;
use BackendBundle\Service\Users\UserServiceInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use HelperBundle\Services\Database\SchemaToolInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserServiceInterfaceTest extends \Codeception\Test\Unit
{
    /** @var \Backend */
    protected $tester;

    public function testAddUser()
    {
        $userData = $this->generateDummyUserData(1)[0];

        /** @var UserServiceInterface $userService */
        $userService = $this->getUserService();
        $user = $userService->createUser($userData);

        $this->assertNotNull($user, 'User should not be null');
        $this->assertNotNull($user->getId(), 'User should not be null');
    }

    private function generateDummyUserData(int $amount): array
    {
        $data = [];
        for ($i = 0; $i < $amount; $i++) {
            $uploadedFile = new UploadedFile(__FILE__, 'test.php');
            $userData = new AddUserData();
            $randomValue = md5(uniqid(rand(), true));
            $userData->setActive(true);
            $userData->setFirstname("Theo $randomValue");
            $userData->setLastname("Test $randomValue");
            $userData->setPassword("Test $randomValue");
            $userData->setEmail("theo.test$randomValue@test.com");
            $userData->setUsername("theo.test$randomValue");
            $userData->setProfilePicture($uploadedFile);
            $data[] = $userData;
        }

        return $data;
    }

    private function getUserService(): UserServiceInterface
    {
        return $this->tester->grabService('jinya_gallery.services.user_service');
    }

    public function testDeleteUser()
    {
        /** @var AddUserData $userData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $userService = $this->getUserService();
        $user = $userService->createUser($dummyUserData);
        $userService->deleteUser($user->getId());

        try {
            $userService->getUser($user->getId());

            $this->fail("The user wasn't deleted");
        } catch (Throwable $ex) {
            $this->assertTrue(true);
        }
    }

    public function testDeleteUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->deleteUser(-1);

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testGetUser()
    {
        /** @var AddUserData $userData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $userService = $this->getUserService();
        $user = $userService->createUser($dummyUserData);
        $userData = $userService->getUser($user->getId());

        $this->assertNotNull($userData, 'The user should not be null');
    }

    public function testGetUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->getUser(-1);

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testAddUserInvalidFields()
    {
        try {
            $uploadedFile = new UploadedFile(__FILE__, 'test.php');
            $userData = new AddUserData();
            $randomValue = md5(uniqid(rand(), true));
            $userData->setActive(true);
            $userData->setFirstname("Theo $randomValue");
            $userData->setLastname("Test $randomValue");
            $userData->setPassword("Test $randomValue");
            $userData->setEmail("theo.test$randomValue@test.com");
            $userData->setProfilePicture($uploadedFile);

            /** @var UserServiceInterface $userService */
            $userService = $this->getUserService();
            $userService->createUser($userData);

            $this->fail('The value null should be invalid');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The value null is invalid');
        }
    }

    public function testGetAllUsersOffset0Count10()
    {
        $this->persistDummyUserData();

        $userService = $this->getUserService();
        $users = $userService->getAllUsers(0, 10);

        $this->assertCount(10, $users);
    }

    private function persistDummyUserData(int $amount = 10)
    {
        /** @var UserServiceInterface $userService */
        $userService = $this->getUserService();
        $dummyUsers = $this->generateDummyUserData($amount);
        foreach ($dummyUsers as $dummyUser) {
            try {
                $userService->createUser($dummyUser);
            } catch (\Throwable $ex) {
                $this->tester->comment("This shouldn't happen");
            }
        }
    }

    public function testGetAllUsersOffset1000Count10()
    {
        $userService = $this->getUserService();
        $users = $userService->getAllUsers(1000, 10);

        $this->assertCount(0, $users);
    }

    public function testGetAllUsersOffset5Count5()
    {
        $this->persistDummyUserData(20);

        $userService = $this->getUserService();
        $users = $userService->getAllUsers(5, 5);

        $this->assertCount(5, $users);
    }

    public function testUpdateUser()
    {
        /** @var AddUserData $userData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $userService = $this->getUserService();
        $user = $userService->createUser($dummyUserData);
        $userData = new UserData();
        $userData->setUsername('theo.test');

        $userData = $userService->updateUser($user->getId(), $userData);

        $changedUser = $userService->getUser($user->getId());

        $this->assertEquals($userData->getUsername(), $changedUser->getUsername(), 'The user should not be null');
    }

    public function testUpdateUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->updateUser(-1, new UserData());

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testActivateUser()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setActive(false);
        $userService = $this->getUserService();
        $user = $userService->createUser($dummyUserData);

        $this->assertFalse($user->isEnabled());

        $userService->activateUser($user->getId());

        $changedUser = $userService->getUser($user->getId());

        $this->assertTrue($changedUser->isActive(), 'The user should not be null');
    }

    public function testActivateUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->activateUser(-1);

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testDeactivateUser()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setActive(true);
        $userService = $this->getUserService();
        $user = $userService->createUser($dummyUserData);

        $this->assertTrue($user->isEnabled());

        $userService->deactivateUser($user->getId());

        $changedUser = $userService->getUser($user->getId());

        $this->assertFalse($changedUser->isActive(), 'The user should not be null');
    }

    public function testDeactivateUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->deactivateUser(-1);

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testChangePassword()
    {
        /** @var AddUserData $userData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $userService = $this->getUserService();
        /** @var UserManagerInterface $userManager */
        $userManager = $this->tester->grabService('fos_user.user_manager');
        $user = $userService->createUser($dummyUserData);
        $oldPassword = $user->getPassword();

        $userService->changePassword($user->getId(), $oldPassword);

        $newPassword = $userManager->findUserByUsername($user->getUsername());

        $this->assertNotEquals($oldPassword, $newPassword);
    }

    public function testChangePasswordUserDoesNotExist()
    {
        $userService = $this->getUserService();

        try {
            $userService->updateUser(-1, new UserData());

            $this->fail('The user should not exist');
        } catch (Throwable $ex) {
            $this->assertTrue(true, 'The user does not exist');
        }
    }

    public function testGrantRoleWriter()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setWriter(false);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->grantRole($user->getId(), User::ROLE_WRITER);

        $this->assertTrue($userService->getUser($user->getId())->isWriter());
    }

    public function testGrantRoleAdmin()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setAdmin(false);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->grantRole($user->getId(), User::ROLE_ADMIN);

        $this->assertTrue($userService->getUser($user->getId())->isAdmin());
    }

    public function testGrantRoleSuperAdmin()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setSuperAdmin(false);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->grantRole($user->getId(), User::ROLE_SUPER_ADMIN);

        $this->assertTrue($userService->getUser($user->getId())->isSuperAdmin());
    }

    public function testRevokeRoleWriter()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setWriter(true);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->revokeRole($user->getId(), User::ROLE_WRITER);

        $this->assertFalse($userService->getUser($user->getId())->isWriter());
    }

    public function testRevokeRoleAdmin()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setAdmin(true);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->revokeRole($user->getId(), User::ROLE_ADMIN);

        $this->assertFalse($userService->getUser($user->getId())->isAdmin());
    }

    public function testRevokeRoleSuperAdmin()
    {
        /** @var AddUserData $dummyUserData */
        $dummyUserData = $this->generateDummyUserData(1)[0];
        $dummyUserData->setSuperAdmin(true);
        $userService = $this->getUserService();

        /** @var UserManagerInterface $userManager */
        $user = $userService->createUser($dummyUserData);
        $userService->revokeRole($user->getId(), User::ROLE_SUPER_ADMIN);

        $this->assertFalse($userService->getUser($user->getId())->isSuperAdmin());
    }

    protected function _before()
    {
        /** @var SchemaToolInterface $schemaTool */
        $schemaTool = $this->tester->grabService('jinya_gallery.services.schema_tool');

        $schemaTool->updateSchema();
    }

    protected function _after()
    {
    }
}
