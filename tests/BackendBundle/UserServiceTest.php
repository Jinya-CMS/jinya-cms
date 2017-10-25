<?php


use BackendBundle\Form\AddUserData;
use BackendBundle\Service\Database\SchemaToolInterface;
use BackendBundle\Service\Users\UserServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserServiceTest extends \Codeception\Test\Unit
{
    /**
     * @var \Backend
     */
    protected $tester;

    public function testAddUser()
    {
        $uploadedFile = new UploadedFile(__FILE__, 'test.php');
        $userData = new AddUserData();
        $userData->setActive(true);
        $userData->setFirstname('Theo');
        $userData->setLastname('Test');
        $userData->setPassword('HelloWorld');
        $userData->setEmail('theo.test@test.com');
        $userData->setUsername('theo.test');
        $userData->setProfilePicture($uploadedFile);

        /** @var UserServiceInterface $userService */
        $userService = $this->tester->grabService('jinya_gallery.services.user_service');
        $user = $userService->createUser($userData);

        $this->assertNotNull($user, 'User should not be null');
        $this->assertNotNull($user->getId(), 'User should not be null');
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