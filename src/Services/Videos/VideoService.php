<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 06.06.18
 * Time: 18:53
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Video\Video;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;

class VideoService implements VideoServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * VideoService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        $this->entityManager = $entityManager;
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, Video::class);
        $this->slugService = $slugService;
    }

    /**
     * Gets a list of videos in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return Video[]
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->createQueryBuilder($keyword)
            ->select('yv')
            ->setMaxResults($count)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $keyword
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilder(string $keyword): \Doctrine\ORM\QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Video::class, 'yv')
            ->where('yv.name LIKE :keyword')
            ->andWhere('yv.description LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all videos filtered by the given keyword
     *
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        return $this->createQueryBuilder($keyword)
            ->select('count(yv)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Saves or update the given video
     *
     * @param Video $video
     * @return Video
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(Video $video): Video
    {
        $this->baseService->saveOrUpdate($video);

        return $video;
    }

    /**
     * Deletes the given video
     *
     * @param Video $video
     */
    public function delete(Video $video): void
    {
        $this->baseService->delete($video);
    }

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return Video
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): ?Video
    {
        return $this->baseService->get($slug);
    }
}
