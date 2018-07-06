<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.06.18
 * Time: 21:19
 */

namespace Jinya\Services\Galleries;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Services\Base\BaseService;

class VideoGalleryService implements VideoGalleryServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseService */
    private $baseService;

    /**
     * VideoGalleryService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->baseService = new BaseService($entityManager, VideoGallery::class);
    }

    /**
     * Gets the specified gallery by slug
     *
     * @param string $slug
     * @return VideoGallery
     */
    public function get(string $slug): VideoGallery
    {
        return $this->entityManager->getRepository(VideoGallery::class)->findOneBy(['slug' => $slug]);
    }

    /**
     * Gets all galleries by the given parameters
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return VideoGallery[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->createQueryBuilder($keyword)
            ->select('video_gallery')
            ->setFirstResult($offset)
            ->setMaxResults($count)
            ->getQuery()
            ->getResult();
    }

    private function createQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(VideoGallery::class, 'video_gallery')
            ->where('video_gallery.name LIKE :keyword')
            ->andWhere('video_gallery.description LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all galleries
     *
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        return $this->createQueryBuilder($keyword)
            ->select('count(video_gallery)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or updates the given gallery
     *
     * @param VideoGallery $gallery
     * @return VideoGallery
     */
    public function saveOrUpdate(VideoGallery $gallery): VideoGallery
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->baseService->saveOrUpdate($gallery);
    }

    /**
     * Deletes the given gallery
     *
     * @param VideoGallery $gallery
     */
    public function delete(VideoGallery $gallery): void
    {
        $this->baseService->delete($gallery);
    }
}
