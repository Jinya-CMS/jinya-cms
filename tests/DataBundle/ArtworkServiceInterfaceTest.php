<?php


use BackendBundle\Form\AddUserData;
use BackendBundle\Service\Users\UserServiceInterface;
use DataBundle\Entity\Artwork;
use DataBundle\Services\Artworks\ArtworkServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class ArtworkServiceInterfaceTest extends \Codeception\Test\Unit
{
    /**
     * @var \DataBundleTester
     */
    protected $tester;

    public function testAddArtwork()
    {
        $artworkService = $this->getArtworkService();
        $dummyData = $this->generateDummyData(1);

        $artwork = $artworkService->saveOrUpdate($dummyData[0]);
        $this->assertNotEmpty($artwork->getId());
    }

    private function getArtworkService(): ArtworkServiceInterface
    {
        return $this->tester->grabService('jinya_gallery.services.artwork_service');
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
            $dummy = new Artwork();
            $dummy->setName("Dummy Artwork $i");
            $dummy->setDescription("Dummy Artwork description $i");
            $dummy->setPicture('http://rndimg.com/ImageStore/OilPaintingBlueReal/192x108_OilPaintingBlueReal_65a5b97ddeaf4856a2294818178cd6c0.jpg');
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

    public function testUpdateAttachedArtwork()
    {
        $artworkService = $this->getArtworkService();
        $dummyData = $this->generateDummyData(1)[0];
        $artwork = $artworkService->saveOrUpdate($dummyData);

        $originalName = $artwork->getName();

        $artwork->setName('Test');
        $artwork = $artworkService->saveOrUpdate($artwork);

        $this->assertEquals('Test', $artwork->getName());

        $history = $artwork->getHistory();
        $this->assertEquals(2, count($history));
        $this->assertNull($history[0]['entry']['name'][0]);
        $this->assertEquals($originalName, $history[0]['entry']['name'][1]);
        $this->assertEquals($originalName, $history[1]['entry']['name'][0]);
        $this->assertEquals('Test', $history[1]['entry']['name'][1]);
    }

    public function testUpdateDetachedArtwork()
    {
        $artworkService = $this->getArtworkService();
        $dummyData = $this->generateDummyData(1)[0];
        $originalArtwork = $artworkService->saveOrUpdate($dummyData);
        $id = $originalArtwork->getId();

        $originalName = $originalArtwork->getName();

        $artwork = new Artwork();
        $artwork->setId($id);
        $artwork->setName('Test');
        $artwork = $artworkService->saveOrUpdate($artwork);

        $this->assertEquals('Test', $artwork->getName());

        $history = $artwork->getHistory();
        $this->assertEquals(2, count($history));
        $this->assertNull($history[0]['entry']['name'][0]);
        $this->assertEquals($originalName, $history[0]['entry']['name'][1]);
        $this->assertEquals($originalName, $history[1]['entry']['name'][0]);
        $this->assertEquals('Test', $history[1]['entry']['name'][1]);
    }

    public function testGetArtworkByIdExists()
    {
        /** @var Artwork $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $artworkService = $this->getArtworkService();
        $dummyEntity = $artworkService->saveOrUpdate($dummyEntity);

        $entity = $artworkService->get($dummyEntity->getId());
        $this->assertNotNull($entity);
    }

    public function testGetArtworkByIdNotExists()
    {
        /** @var Artwork $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $artworkService = $this->getArtworkService();
        $dummyEntity = $artworkService->saveOrUpdate($dummyEntity);

        try {
            $entity = $artworkService->get($dummyEntity->getId() + 10);
            $this->assertNull($entity);
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testGetArtworkBySlugExists()
    {
        /** @var Artwork $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $artworkService = $this->getArtworkService();
        $dummyEntity = $artworkService->saveOrUpdate($dummyEntity);

        $entity = $artworkService->get($dummyEntity->getSlug());
        $this->assertNotNull($entity);
    }

    public function testGetArtworkBySlugNotExists()
    {
        /** @var Artwork $dummyEntity */
        $dummyEntity = $this->generateDummyData(1)[0];
        $artworkService = $this->getArtworkService();
        $dummyEntity = $artworkService->saveOrUpdate($dummyEntity);

        try {
            $entity = $artworkService->get('Testslug');
            $this->assertNull($entity);
        } catch (Exception $exception) {
            $this->assertTrue(true);
        }
    }

    public function testGetAllWithoutKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $artworkService = $this->getArtworkService();
        foreach ($dummies as $dummy) {
            $artworkService->saveOrUpdate($dummy);
        }

        $data = $artworkService->getAll(0, 12, '');
        $this->assertCount(12, $data);
    }

    public function testGetAllWithKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $artworkService = $this->getArtworkService();
        foreach ($dummies as $dummy) {
            $artworkService->saveOrUpdate($dummy);
        }

        $data = $artworkService->getAll(0, 12, '5');
        $this->assertCount(1, $data);
    }

    public function testGetAllWithoutResults()
    {
        $dummies = $this->generateDummyData(15);
        $artworkService = $this->getArtworkService();
        foreach ($dummies as $dummy) {
            $artworkService->saveOrUpdate($dummy);
        }

        $data = $artworkService->getAll(16, 12);
        $this->assertCount(0, $data);
    }

    public function testCountAllWithoutKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $artworkService = $this->getArtworkService();
        foreach ($dummies as $dummy) {
            $artworkService->saveOrUpdate($dummy);
        }

        $data = $artworkService->countAll();
        $this->assertEquals(12, $data);
    }

    public function testCountAllWithKeyword()
    {
        $dummies = $this->generateDummyData(12);
        $artworkService = $this->getArtworkService();
        foreach ($dummies as $dummy) {
            $artworkService->saveOrUpdate($dummy);
        }

        $data = $artworkService->countAll('5');
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