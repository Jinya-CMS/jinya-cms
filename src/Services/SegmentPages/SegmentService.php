<?php

/** @noinspection UnusedConstructorDependenciesInspection */

/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 14.11.2017
 * Time: 17:33
 */

namespace Jinya\Services\SegmentPages;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\SegmentPage\Segment;
use Jinya\Framework\Events\SegmentPages\RearrangeEvent;
use Jinya\Framework\Events\SegmentPages\SegmentEvent;
use Jinya\Framework\Events\SegmentPages\SegmentPositionUpdateEvent;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Galleries\VideoGalleryServiceInterface;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Jinya\Services\Videos\VideoServiceInterface;
use Jinya\Services\Videos\YoutubeVideoServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SegmentService implements SegmentServiceInterface
{
    use ArrangementServiceTrait;

    /** @var SegmentPageServiceInterface */
    private $segmentPageService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var ArtGalleryServiceInterface */
    private $artGalleryService;

    /** @var VideoGalleryServiceInterface */
    private $videoGalleryService;

    /** @var FormServiceInterface */
    private $formService;

    /** @var VideoServiceInterface */
    private $videoService;

    /** @var YoutubeVideoServiceInterface */
    private $youtubeVideoService;

    /** @var GalleryServiceInterface */
    private $galleryService;

    /** @var FileServiceInterface */
    private $fileService;

    /**
     * SegmentService constructor.
     * @param SegmentPageServiceInterface $segmentPageService
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param ArtworkServiceInterface $artworkService
     * @param ArtGalleryServiceInterface $artGalleryService
     * @param VideoGalleryServiceInterface $videoGalleryService
     * @param FormServiceInterface $formService
     * @param VideoServiceInterface $videoService
     * @param YoutubeVideoServiceInterface $youtubeVideoService
     * @param GalleryServiceInterface $galleryService
     * @param FileServiceInterface $fileService
     */
    public function __construct(
        SegmentPageServiceInterface $segmentPageService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        ArtworkServiceInterface $artworkService,
        ArtGalleryServiceInterface $artGalleryService,
        VideoGalleryServiceInterface $videoGalleryService,
        FormServiceInterface $formService,
        VideoServiceInterface $videoService,
        YoutubeVideoServiceInterface $youtubeVideoService,
        GalleryServiceInterface $galleryService,
        FileServiceInterface $fileService
    ) {
        $this->segmentPageService = $segmentPageService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->artworkService = $artworkService;
        $this->artGalleryService = $artGalleryService;
        $this->videoGalleryService = $videoGalleryService;
        $this->formService = $formService;
        $this->videoService = $videoService;
        $this->youtubeVideoService = $youtubeVideoService;
        $this->galleryService = $galleryService;
        $this->fileService = $fileService;
    }

    /**
     * Saves the artwork in the given segment page at the given position
     *
     * @param string $artworkSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveArtworkSegment(
        string $artworkSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment($artworkSlug, $segmentPageSlug, $position, $action, $target, $script, 'artwork');
    }

    /**
     * @param string|int $slug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @param string $type
     * @return Segment
     */
    private function saveNewSegment(
        $slug,
        string $segmentPageSlug,
        int $position,
        string $action,
        string $target,
        string $script,
        string $type
    ): Segment {
        $segmentPage = $this->segmentPageService->get($segmentPageSlug);

        $service = $type . 'Service';
        $setter = 'set' . ucfirst($type);
        $entity = $this->{$service}->get($slug);

        $segment = new Segment();
        $segment->setScript($script);
        $segment->setTarget($target);
        $segment->setAction($action);
        $segment->{$setter}($entity);
        $segment->setPage($segmentPage);

        $positions = $segmentPage->getSegments()->toArray();
        $positions = $this->rearrange($positions, -1, $position, $segment);

        $segmentPage->setSegments(new ArrayCollection($positions));
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(
            new RearrangeEvent($segmentPage, $segment, -1, $position),
            RearrangeEvent::POST_REARRANGE
        );

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segment->getId()), SegmentEvent::POST_SAVE);

        return $segment;
    }

    /**
     * Saves the file in the given segment page at the given position
     *
     * @param int $fileId
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveFileSegment(
        int $fileId,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment($fileId, $segmentPageSlug, $position, $action, $target, $script, 'file');
    }

    /**
     * Saves the art gallery in the given segment page at the given position
     *
     * @param string $artGallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveArtGallerySegment(
        string $artGallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment(
            $artGallerySlug,
            $segmentPageSlug,
            $position,
            $action,
            $target,
            $script,
            'artGallery'
        );
    }

    /**
     * Saves the gallery in the given segment page at the given position
     *
     * @param string $gallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveGallerySegment(
        string $gallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment(
            $gallerySlug,
            $segmentPageSlug,
            $position,
            $action,
            $target,
            $script,
            'gallery'
        );
    }

    /**
     * Saves the video in the given segment page at the given position
     *
     * @param string $videoSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveVideoSegment(
        string $videoSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment($videoSlug, $segmentPageSlug, $position, $action, $target, $script, 'video');
    }

    /**
     * Saves the youtube video in the given segment page at the given position
     *
     * @param string $youtubeVideoSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveYoutubeVideoSegment(
        string $youtubeVideoSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment(
            $youtubeVideoSlug,
            $segmentPageSlug,
            $position,
            $action,
            $target,
            $script,
            'youtubeVideo'
        );
    }

    /**
     * Saves the video gallery in the given segment page at the given position
     *
     * @param string $videoGallerySlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveVideoGallerySegment(
        string $videoGallerySlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment(
            $videoGallerySlug,
            $segmentPageSlug,
            $position,
            $action,
            $target,
            $script,
            'videoGallery'
        );
    }

    /**
     * Saves the form in the given segment page at the given position
     *
     * @param string $formSlug
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveFormSegment(
        string $formSlug,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        return $this->saveNewSegment($formSlug, $segmentPageSlug, $position, $action, $target, $script, 'form');
    }

    /**
     * Updates the artwork in the given segment page at the given position
     *
     * @param string $artworkSlug
     * @param int $segmentId
     * @return int
     */
    public function updateArtworkSegment(string $artworkSlug, int $segmentId): int
    {
        return $this->updateSegment($artworkSlug, $segmentId, 'artwork');
    }

    /**
     * @param string|int $slug
     * @param int $segmentId
     * @param string $type
     * @return int
     */
    private function updateSegment($slug, int $segmentId, string $type): int
    {
        $segment = $this->get($segmentId);
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segmentId), SegmentEvent::PRE_SAVE);
        $service = $type . 'Service';
        $setter = 'set' . ucfirst($type);
        $entity = $this->{$service}->get($slug);

        $segment->{$setter}($entity);
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segmentId), SegmentEvent::POST_SAVE);

        return $segment->getId();
    }

    public function get(int $id): Segment
    {
        $this->eventDispatcher->dispatch(new SegmentEvent(null, $id), SegmentEvent::PRE_GET);
        $segment = $this->entityManager->find(Segment::class, $id);
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $id), SegmentEvent::POST_GET);

        return $segment;
    }

    /**
     * Updates the art gallery in the given segment page at the given position
     *
     * @param string $artGallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateArtGallerySegment(string $artGallerySlug, int $segmentId): int
    {
        return $this->updateSegment($artGallerySlug, $segmentId, 'artGallery');
    }

    /**
     * Updates the video in the given segment page at the given position
     *
     * @param string $videoSlug
     * @param int $segmentId
     * @return int
     */
    public function updateVideoSegment(string $videoSlug, int $segmentId): int
    {
        return $this->updateSegment($videoSlug, $segmentId, 'video');
    }

    /**
     * Updates the youtube video in the given segment page at the given position
     *
     * @param string $youtubeVideoSlug
     * @param int $segmentId
     * @return int
     */
    public function updateYoutubeVideoSegment(string $youtubeVideoSlug, int $segmentId): int
    {
        return $this->updateSegment($youtubeVideoSlug, $segmentId, 'youtubeVideo');
    }

    /**
     * Updates the video gallery in the given segment page at the given position
     *
     * @param string $videoGallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateVideoGallerySegment(string $videoGallerySlug, int $segmentId): int
    {
        return $this->updateSegment($videoGallerySlug, $segmentId, 'videoGallery');
    }

    /**
     * Updates the form in the given segment page at the given position
     *
     * @param string $formSlug
     * @param int $segmentId
     * @return int
     */
    public function updateFormSegment(string $formSlug, int $segmentId): int
    {
        return $this->updateSegment($formSlug, $segmentId, 'form');
    }

    /**
     * Saves the html in the given segment page at the given position
     *
     * @param string $html
     * @param string $segmentPageSlug
     * @param int $position
     * @param string $action
     * @param string $target
     * @param string $script
     * @return Segment
     */
    public function saveHtmlSegment(
        string $html,
        string $segmentPageSlug,
        int $position,
        string $action = Segment::ACTION_NONE,
        string $target = '',
        string $script = ''
    ): Segment {
        $segmentPage = $this->segmentPageService->get($segmentPageSlug);

        $segment = new Segment();
        $segment->setScript($script);
        $segment->setTarget($target);
        $segment->setAction($action);
        $segment->setHtml($html);
        $segment->setPage($segmentPage);

        $positions = $segmentPage->getSegments()->toArray();
        $positions = $this->rearrange($positions, -1, $position, $segment);

        $segmentPage->setSegments(new ArrayCollection($positions));
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(
            new RearrangeEvent($segmentPage, $segment, -1, $position),
            RearrangeEvent::POST_REARRANGE
        );

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segment->getId()), SegmentEvent::POST_SAVE);

        return $segment;
    }

    /**
     * @param int $segmentId
     * @param string $action
     * @param string $target
     * @param string $script
     */
    public function updateAction(int $segmentId, ?string $action, ?string $target = '', ?string $script = ''): void
    {
        $this->eventDispatcher->dispatch(new SegmentEvent(null, $segmentId), SegmentEvent::PRE_SAVE);
        $segment = $this->get($segmentId);
        $segment->setAction($action);
        $segment->setTarget($target);
        $segment->setScript($script);

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segmentId), SegmentEvent::POST_SAVE);
    }

    /**
     * Sets the segments position to the new position
     *
     * @param string $segmentPageSlug
     * @param int $segmentId
     * @param int $newPosition
     * @param int $oldPosition
     */
    public function updatePosition(string $segmentPageSlug, int $segmentId, int $oldPosition, int $newPosition): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new SegmentPositionUpdateEvent($segmentPageSlug, $segmentId, $oldPosition, $newPosition),
            SegmentPositionUpdateEvent::PRE_UPDATE
        );

        if (!$pre->isCancel()) {
            $segmentPage = $this->segmentPageService->get($segmentPageSlug);
            $positions = $segmentPage->getSegments()->toArray();

            $segment = $this->get($segmentId);

            $segments = $this->rearrange($positions, $oldPosition, $newPosition, $segment);
            $segmentPage->setSegments(new ArrayCollection($segments));
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                new SegmentPositionUpdateEvent($segmentPageSlug, $segmentId, $oldPosition, $newPosition),
                SegmentPositionUpdateEvent::POST_UPDATE
            );
        }
    }

    /**
     * Updates the html in the given segment page at the given position
     *
     * @param string $html
     * @param int $segmentId
     * @return int
     */
    public function updateHtmlSegment(string $html, int $segmentId): int
    {
        $segment = $this->get($segmentId);
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segmentId), SegmentEvent::PRE_SAVE);
        $segment->setHtml($html);
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(new SegmentEvent($segment, $segmentId), SegmentEvent::POST_SAVE);

        return $segment->getId();
    }

    /**
     * Deletes the given segment
     *
     * @param int $id
     */
    public function deleteSegment(int $id): void
    {
        $segment = $this->get($id);
        $this->entityManager->remove($segment);
        $this->entityManager->flush();
    }

    /**
     * Updates the gallery in the given segment page at the given position
     *
     * @param string $gallerySlug
     * @param int $segmentId
     * @return int
     */
    public function updateGallerySegment(string $gallerySlug, int $segmentId): int
    {
        return $this->updateSegment($gallerySlug, $segmentId, 'gallery');
    }

    /**
     * Updates the file in the given segment page at the given position
     *
     * @param int $fileId
     * @param int $segmentId
     * @param string $script
     * @param string $action
     * @param string $target
     * @return int
     */
    public function updateFileSegment(int $fileId, int $segmentId, string $script, string $action, string $target): int
    {
        $segment = $this->get($segmentId);
        $segment->setScript($script);
        $segment->setTarget($target);
        $segment->setAction($action);
        $this->entityManager->flush();

        return $this->updateSegment($fileId, $segmentId, 'file');
    }
}
