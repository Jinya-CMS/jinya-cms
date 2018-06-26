<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:09
 */

namespace Jinya\Services\Videos;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Galleries\VideoGallery;
use Jinya\Entity\Galleries\VideoPosition;
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;

class VideoPositionService implements VideoPositionServiceInterface
{
    use ArrangementServiceTrait;

    /** @var VideoGalleryServiceInterface */
    private $galleryService;

    /** @var VideoServiceInterface */
    private $videoService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * VideoPositionService constructor.
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoServiceInterface $videoService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(VideoGalleryServiceInterface $galleryService, VideoServiceInterface $videoService, EntityManagerInterface $entityManager)
    {
        $this->galleryService = $galleryService;
        $this->videoService = $videoService;
        $this->entityManager = $entityManager;
    }

    /**
     * Saves the video in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $videoSlug
     * @param int $position
     * @return int
     */
    public function savePosition(string $gallerySlug, string $videoSlug, int $position): int
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $video = $this->videoService->get($videoSlug);

        $videoPosition = new VideoPosition();
        $videoPosition->setVideo($video);
        $videoPosition->setGallery($gallery);

        $this->rearrangeVideos(-1, $position, $videoPosition, $gallery);

        return $videoPosition->getId();
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param VideoPosition $videoPosition
     * @param VideoGallery $gallery
     */
    private function rearrangeVideos(int $oldPosition, int $newPosition, VideoPosition $videoPosition, VideoGallery $gallery): void
    {
        $positions = $gallery->getVideos()->toArray();
        $positions = $this->rearrange($positions, $oldPosition, $newPosition, $videoPosition);

        $gallery->setVideos(new ArrayCollection($positions));
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition(string $gallerySlug, int $videoPositionId, int $oldPosition, int $newPosition)
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $videos = $gallery->getVideos();

        $video = $videos->filter(function (VideoPosition $item) use ($oldPosition) {
            return $item->getPosition() === $oldPosition;
        })->first();

        $this->rearrangeVideos($oldPosition, $newPosition, $video, $gallery);
    }

    /**
     * Deletes the given video position
     *
     * @param int $id
     */
    public function deletePosition(int $id)
    {
        $this->entityManager
            ->createQueryBuilder()
            ->delete(VideoPosition::class, 'e')
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }

    /**
     * Gets the video position for the given id
     *
     * @param int $id
     * @return VideoPosition
     */
    public function getPosition(int $id): VideoPosition
    {
        return $this->entityManager->find(VideoPosition::class, $id);
    }

    /**
     * Sets the video of the given video position to the new slug
     *
     * @param int $id
     * @param string $videoSlug
     */
    public function updateVideo(int $id, string $videoSlug)
    {
        $video = $this->videoService->get($videoSlug);
        $this->entityManager
            ->createQueryBuilder()
            ->update(VideoPosition::class, 'e')
            ->set('e.video', $video->getId())
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->execute();
    }
}
