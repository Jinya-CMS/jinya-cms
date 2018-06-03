<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 01.06.18
 * Time: 19:04
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Video\YoutubeVideo;
use Jinya\Services\Base\BaseSlugEntityService;
use Jinya\Services\Slug\SlugServiceInterface;

class YoutubeVideoService implements YoutubeVideoServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BaseSlugEntityService */
    private $baseService;

    /** @var SlugServiceInterface */
    private $slugService;

    /**
     * YoutubeVideoService constructor.
     * @param EntityManagerInterface $entityManager
     * @param SlugServiceInterface $slugService
     */
    public function __construct(EntityManagerInterface $entityManager, SlugServiceInterface $slugService)
    {
        $this->entityManager = $entityManager;
        $this->baseService = new BaseSlugEntityService($entityManager, $slugService, YoutubeVideo::class);
        $this->slugService = $slugService;
    }

    /**
     * Gets a list of videos in the given range and filtered by the given keyword
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return YoutubeVideo[]
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
            ->from(YoutubeVideo::class, 'yv')
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
     * @param YoutubeVideo $video
     * @return YoutubeVideo
     * @throws \Jinya\Exceptions\EmptySlugException
     */
    public function saveOrUpdate(YoutubeVideo $video): YoutubeVideo
    {
        $this->baseService->saveOrUpdate($video);

        return $video;
    }

    /**
     * Deletes the given video
     *
     * @param YoutubeVideo $video
     */
    public function delete(YoutubeVideo $video): void
    {
        $this->baseService->delete($video);
    }

    /**
     * Gets the artwork by slug or id
     *
     * @param string $slug
     * @return YoutubeVideo
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function get(string $slug): ?YoutubeVideo
    {
        return $this->baseService->get($slug);
    }
}
