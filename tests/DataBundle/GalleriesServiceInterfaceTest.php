<?php


use BackendBundle\Form\AddUserData;
use BackendBundle\Service\Users\UserServiceInterface;
use DataBundle\Entity\Gallery;
use DataBundle\Services\Galleries\GalleryServiceInterface;
use HelperBundle\Services\Database\SchemaToolInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class GalleriesServiceInterfaceTest extends \Codeception\Test\Unit
{
    /**
     * @var \DataBundleTester
     */
    protected $tester;

    public function testAddGallery()
    {
        $galleryService = $this->getGalleryService();
        $dummyData = $this->generateDummyData(1);

        $gallery = $galleryService->saveOrUpdate($dummyData[0]);
        $this->assertNotEmpty($gallery->getId());
    }

    private function getGalleryService(): GalleryServiceInterface
    {
        return $this->tester->grabService('jinya_gallery.services.gallery_service');
    }

    private function generateDummyData(int $count = 10): array
    {
        $dummyUser = $this->generateDummyUser();
        $user = $this->getUserService()->createUser($dummyUser);
        /** @var TokenStorage $tokenStorage */
        $tokenStorage = $this->tester->grabService('security.token_storage');

        $tokenStorage->setToken(new UsernamePasswordToken($user, $dummyUser->getPassword(), 'fos_user'));

        $dummyData = [];
        for ($i = 0; $i < $count; $i++) {
            $dummy = new Gallery();
            $dummy->setName("Dummy Gallery $i");
            $dummy->setBackground('#fff');
            $dummy->setDescription("Dummy Gallery description $i");
            $dummy->setSlug("dummy_gallery_$i");
            $dummy->setCreator($user);
            $dummyData[] = $dummy;
        }

        return $dummyData;
    }

    private function generateDummyUser(): AddUserData
    {
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

        return $userData;
    }

    private function getUserService(): UserServiceInterface
    {
        return $this->tester->grabService('jinya_gallery.services.user_service');
    }

    public function testUpdateAttachedGallery()
    {
        $galleryService = $this->getGalleryService();
        $dummyData = $this->generateDummyData(1)[0];
        $gallery = $galleryService->saveOrUpdate($dummyData);

        $originalName = $gallery->getName();

        $gallery->setName('Test');
        $gallery = $galleryService->saveOrUpdate($gallery);

        $this->assertEquals('Test', $gallery->getName());

        $history = $gallery->getHistory();
        $this->assertEquals(2, count($history));
        $this->assertNull($history[0]['entry']['name'][0]);
        $this->assertEquals($originalName, $history[0]['entry']['name'][1]);
        $this->assertEquals($originalName, $history[1]['entry']['name'][0]);
        $this->assertEquals('Test', $history[1]['entry']['name'][1]);
    }

    public function testUpdateDetachedGallery()
    {
        $galleryService = $this->getGalleryService();
        $dummyData = $this->generateDummyData(1)[0];
        $originalGallery = $galleryService->saveOrUpdate($dummyData);
        $id = $originalGallery->getId();

        $originalName = $originalGallery->getName();

        $gallery = new Gallery();
        $gallery->setId($id);
        $gallery->setName('Test');
        $gallery = $galleryService->saveOrUpdate($gallery);

        $this->assertEquals('Test', $gallery->getName());

        $history = $gallery->getHistory();
        $this->assertEquals(2, count($history));
        $this->assertNull($history[0]['entry']['name'][0]);
        $this->assertEquals($originalName, $history[0]['entry']['name'][1]);
        $this->assertEquals($originalName, $history[1]['entry']['name'][0]);
        $this->assertEquals('Test', $history[1]['entry']['name'][1]);
    }

    public function testGetGalleryByIdExists()
    {
        /** @var Gallery $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $galleryService = $this->getGalleryService();
        $dummyEntity = $galleryService->saveOrUpdate($dummyEntity);

        $entity = $galleryService->get($dummyEntity->getId());
        $this->assertNotNull($entity);
    }

    public function testGetGalleryByIdNotExists()
    {
        /** @var Gallery $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $galleryService = $this->getGalleryService();
        $dummyEntity = $galleryService->saveOrUpdate($dummyEntity);

        try {
            $entity = $galleryService->get($dummyEntity->getId() + 10);
            $this->assertNull($entity);
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testGetGalleryBySlugExists()
    {
        /** @var Gallery $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $galleryService = $this->getGalleryService();
        $dummyEntity = $galleryService->saveOrUpdate($dummyEntity);

        $entity = $galleryService->get($dummyEntity->getSlug());
        $this->assertNotNull($entity);
    }

    public function testGetGalleryBySlugNotExists()
    {
        /** @var Gallery $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $galleryService = $this->getGalleryService();
        $galleryService->saveOrUpdate($dummyEntity);

        try {
            $entity = $galleryService->get('Testslug');
            $this->assertNull($entity);
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testGetAllWithoutKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $galleryService = $this->getGalleryService();
        foreach ($dummies as $dummy) {
            $galleryService->saveOrUpdate($dummy);
        }

        $data = $galleryService->getAll(0, 12, '');
        $this->assertCount(12, $data);
    }

    public function testGetAllWithKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $galleryService = $this->getGalleryService();
        foreach ($dummies as $dummy) {
            $galleryService->saveOrUpdate($dummy);
        }

        $data = $galleryService->getAll(0, 12, '5');
        $this->assertCount(1, $data);
    }

    public function testGetAllWithoutResults()
    {
        $dummies = $this->generateDummyData(15);
        $galleryService = $this->getGalleryService();
        foreach ($dummies as $dummy) {
            $galleryService->saveOrUpdate($dummy);
        }

        $data = $galleryService->getAll(16, 12);
        $this->assertCount(0, $data);
    }

    public function testCountAllWithoutKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $galleryService = $this->getGalleryService();
        foreach ($dummies as $dummy) {
            $galleryService->saveOrUpdate($dummy);
        }

        $data = $galleryService->countAll();
        $this->assertEquals(12, $data);
    }

    public function testCountAllWithKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $galleryService = $this->getGalleryService();
        foreach ($dummies as $dummy) {
            $galleryService->saveOrUpdate($dummy);
        }

        $data = $galleryService->countAll('5');
        $this->assertEquals(1, $data);
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