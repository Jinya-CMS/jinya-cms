<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 27.06.18
 * Time: 09:15
 */

namespace Jinya\Services\Videos;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Jinya\Entity\Video\Video;
use Jinya\Entity\Video\YoutubeVideo;

class AllVideoService implements AllVideoServiceInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * AllVideoService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Lists all videos of all types
     *
     * @param int $offset
     * @param int $count
     * @param string $keyword
     * @return array
     */
    public function getAll(int $offset = 0, int $count = 10, string $keyword = ''): array
    {
        return $this->createQueryBuilder($keyword)
            ->select('youtube_video, video')
            ->setMaxResults($count)
            ->setFirstResult($offset)
            ->getQuery()
            ->getArrayResult();
    }

    private function createQueryBuilder(string $keyword): QueryBuilder
    {
        return $this->entityManager->createQueryBuilder()
            ->from(Video::class, 'video')
            ->from(YoutubeVideo::class, 'youtube_video')
            ->where('video.description LIKE :keyword')
            ->orWhere('video.name LIKE :keyword')
            ->orWhere('youtube_video.description LIKE :keyword')
            ->orWhere('youtube_video.name LIKE :keyword')
            ->setParameter('keyword', "%$keyword%");
    }

    /**
     * Counts all videos of all types
     *
     * @param string $keyword
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countAll(string $keyword = ''): int
    {
        return array_sum($this->createQueryBuilder($keyword)
            ->select('count(video), count(youtube_video)')
            ->getQuery()
            ->getSingleResult());
    }
}
