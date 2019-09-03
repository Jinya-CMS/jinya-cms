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
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\Gallery\VideoGallery;
use Jinya\Entity\Video\VideoPosition;
use Jinya\Framework\Events\Videos\RearrangeEvent;
use Jinya\Framework\Events\Videos\VideoPositionDeleteEvent;
use Jinya\Framework\Events\Videos\VideoPositionEvent;
use Jinya\Framework\Events\Videos\VideoPositionUpdateEvent;
use Jinya\Framework\Events\Videos\VideoPositionUpdateVideoEvent;
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class VideoPositionService implements VideoPositionServiceInterface
{
    use ArrangementServiceTrait;

    /** @var VideoGalleryServiceInterface */
    private $galleryService;

    /** @var VideoServiceInterface */
    private $videoService;

    /** @var YoutubeVideoServiceInterface */
    private $youtubeVideoService;

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * VideoPositionService constructor.
     * @param VideoGalleryServiceInterface $galleryService
     * @param VideoServiceInterface $videoService
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        VideoGalleryServiceInterface $galleryService,
        VideoServiceInterface $videoService,
        YoutubeVideoServiceInterface $youtubeVideoService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->galleryService = $galleryService;
        $this->videoService = $videoService;
        $this->youtubeVideoService = $youtubeVideoService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Saves the video in the given gallery at the given position
     *
     * @param string $gallerySlug
     * @param string $videoSlug
     * @param int $position
     * @param string $type
     * @return int
     */
    public function savePosition(string $gallerySlug, string $videoSlug, int $position, string $type): int
    {
        $gallery = $this->galleryService->get($gallerySlug);
        $videoPosition = new VideoPosition();

        if ('youtube' === $type) {
            $video = $this->youtubeVideoService->get($videoSlug);
            $videoPosition->setYoutubeVideo($video);
        } else {
            $video = $this->videoService->get($videoSlug);
            $videoPosition->setVideo($video);
        }

        $pre = $this->eventDispatcher->dispatch(
            VideoPositionEvent::PRE_SAVE,
            new VideoPositionEvent($videoPosition, -1)
        );

        if (!$pre->isCancel()) {
            $videoPosition->setGallery($gallery);
            $this->rearrangeVideos(-1, $position, $videoPosition, $gallery);

            $this->eventDispatcher->dispatch(
                VideoPositionEvent::POST_SAVE,
                new VideoPositionEvent($videoPosition, $videoPosition->getId())
            );
        }

        return $videoPosition->getId();
    }

    /**
     * @param int $oldPosition
     * @param int $newPosition
     * @param VideoPosition $videoPosition
     * @param VideoGallery $gallery
     */
    private function rearrangeVideos(
        int $oldPosition,
        int $newPosition,
        VideoPosition $videoPosition,
        VideoGallery $gallery
    ): void {
        $pre = $this->eventDispatcher->dispatch(
            RearrangeEvent::PRE_REARRANGE,
            new RearrangeEvent($gallery, $videoPosition, $oldPosition, $newPosition)
        );

        if (!$pre->isCancel()) {
            $positions = $gallery->getVideos()->toArray();
            $positions = $this->rearrange($positions, $oldPosition, $newPosition, $videoPosition);

            $gallery->setVideos(new ArrayCollection($positions));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                RearrangeEvent::POST_REARRANGE,
                new RearrangeEvent($gallery, $videoPosition, $oldPosition, $newPosition)
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition(string $gallerySlug, int $videoPositionId, int $oldPosition, int $newPosition): void
    {
        $pre = $this->eventDispatcher->dispatch(
            VideoPositionUpdateEvent::PRE_UPDATE,
            new VideoPositionUpdateEvent($gallerySlug, $videoPositionId, $oldPosition, $newPosition)
        );

        if (!$pre->isCancel()) {
            $gallery = $this->galleryService->get($gallerySlug);
            $videos = $gallery->getVideos();

            $video = $videos->filter(static function (VideoPosition $item) use ($oldPosition) {
                return $item->getPosition() === $oldPosition;
            })->first();

            $this->rearrangeVideos($oldPosition, $newPosition, $video, $gallery);
            $this->eventDispatcher->dispatch(
                VideoPositionUpdateEvent::POST_UPDATE,
                new VideoPositionUpdateEvent($gallerySlug, $videoPositionId, $oldPosition, $newPosition)
            );
        }
    }

    /**
     * Deletes the given video position
     *
     * @param int $id
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function deletePosition(int $id): void
    {
        $gallery = $this->entityManager
            ->createQueryBuilder()
            ->select('gallery')
            ->from(VideoGallery::class, 'gallery')
            ->join('gallery.artworks', 'position')
            ->where('position.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
        $pre = $this->eventDispatcher->dispatch(
            VideoPositionDeleteEvent::PRE_DELETE,
            new VideoPositionDeleteEvent($gallery, $id)
        );

        if (!$pre->isCancel()) {
            $this->entityManager
                ->createQueryBuilder()
                ->delete(VideoPosition::class, 'e')
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();

            $this->eventDispatcher->dispatch(
                VideoPositionDeleteEvent::POST_DELETE,
                new VideoPositionDeleteEvent($gallery, $id)
            );
        }
    }

    /**
     * Sets the video of the given video position to the new slug
     *
     * @param int $id
     * @param string $videoSlug
     * @param string $type
     */
    public function updateVideo(int $id, string $videoSlug, string $type): void
    {
        $position = $this->getPosition($id);
        $position->setYoutubeVideo(null);
        $position->setVideo(null);

        $pre = $this->eventDispatcher->dispatch(
            VideoPositionUpdateVideoEvent::PRE_UPDATE_VIDEO,
            new VideoPositionUpdateVideoEvent($videoSlug, $type, $id)
        );

        if (!$pre->isCancel()) {
            if ('youtube' === $type) {
                $position->setYoutubeVideo($this->youtubeVideoService->get($videoSlug));
            } else {
                $position->setVideo($this->videoService->get($videoSlug));
            }

            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                VideoPositionUpdateVideoEvent::POST_UPDATE_VIDEO,
                new VideoPositionUpdateVideoEvent($videoSlug, $type, $id)
            );
        }
    }

    /**
     * Gets the video position for the given id
     *
     * @param int $id
     * @return VideoPosition
     */
    public function getPosition(int $id): VideoPosition
    {
        $this->eventDispatcher->dispatch(VideoPositionEvent::PRE_GET, new VideoPositionEvent(null, $id));
        $position = $this->entityManager->find(VideoPosition::class, $id);
        $this->eventDispatcher->dispatch(VideoPositionEvent::POST_GET, new VideoPositionEvent($position, $id));

        return $position;
    }
}
