<?php
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
use Jinya\Services\Videos\VideoServiceInterface;
use Jinya\Services\Videos\YoutubeVideoServiceInterface;
use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SegmentService implements SegmentServiceInterface
{
    use ArrangementServiceTrait;

    /** @var SegmentPageserviceInterface */
    private $segmentPageService;

    /** @var EntityManagerInterface */
    private $entityManager;
    /** @noinspection PhpUndefinedClassInspection */

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
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

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
        YoutubeVideoServiceInterface $youtubeVideoService
    ) {
        $this->segmentPageService = $segmentPageService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->artworkService = $artworkService;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->artGalleryService = $artGalleryService;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->videoGalleryService = $videoGalleryService;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->formService = $formService;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->videoService = $videoService;
        /** @noinspection UnusedConstructorDependenciesInspection */
        $this->youtubeVideoService = $youtubeVideoService;
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

    private function saveNewSegment(
        string $slug,
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
            RearrangeEvent::POST_REARRANGE,
            new RearrangeEvent($segmentPage, $segment, -1, $position)
        );

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(SegmentEvent::POST_SAVE, new SegmentEvent($segment, $segment->getId()));

        return $segment;
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

    private function updateSegment(string $slug, int $segmentId, string $type): int
    {
        $segment = $this->get($segmentId);
        $this->eventDispatcher->dispatch(SegmentEvent::PRE_SAVE, new SegmentEvent($segment, $segmentId));
        $service = $type . 'Service';
        $setter = 'set' . ucfirst($type);
        $entity = $this->{$service}->get($slug);

        $segment->{$setter}($entity);
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(SegmentEvent::POST_SAVE, new SegmentEvent($segment, $segmentId));

        return $segment->getId();
    }

    public function get(int $id): Segment
    {
        $this->eventDispatcher->dispatch(SegmentEvent::PRE_GET, new SegmentEvent(null, $id));
        $segment = $this->entityManager->find(Segment::class, $id);
        $this->eventDispatcher->dispatch(SegmentEvent::POST_GET, new SegmentEvent($segment, $id));

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
            RearrangeEvent::POST_REARRANGE,
            new RearrangeEvent($segmentPage, $segment, -1, $position)
        );

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(SegmentEvent::POST_SAVE, new SegmentEvent($segment, $segment->getId()));

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
        $this->eventDispatcher->dispatch(SegmentEvent::PRE_SAVE, new SegmentEvent(null, $segmentId));
        $segment = $this->get($segmentId);
        $segment->setAction($action);
        $segment->setTarget($target);
        $segment->setScript($script);

        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(SegmentEvent::POST_SAVE, new SegmentEvent($segment, $segmentId));
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
            SegmentPositionUpdateEvent::PRE_UPDATE,
            new SegmentPositionUpdateEvent($segmentPageSlug, $segmentId, $oldPosition, $newPosition)
        );

        if (!$pre->isCancel()) {
            $segmentPage = $this->segmentPageService->get($segmentPageSlug);
            $positions = $segmentPage->getSegments()->toArray();

            $segment = $this->get($segmentId);

            $segments = $this->rearrange($positions, $oldPosition, $newPosition, $segment);
            $segmentPage->setSegments(new ArrayCollection($segments));
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(
                SegmentPositionUpdateEvent::POST_UPDATE,
                new SegmentPositionUpdateEvent($segmentPageSlug, $segmentId, $oldPosition, $newPosition)
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
        $this->eventDispatcher->dispatch(SegmentEvent::PRE_SAVE, new SegmentEvent($segment, $segmentId));
        $segment->setHtml($html);
        $this->entityManager->flush();
        $this->eventDispatcher->dispatch(SegmentEvent::POST_SAVE, new SegmentEvent($segment, $segmentId));

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
}
