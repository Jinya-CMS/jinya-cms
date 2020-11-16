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
use Jinya\Services\Base\ArrangementServiceTrait;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SegmentService implements SegmentServiceInterface
{
    use ArrangementServiceTrait;

    private SegmentPageServiceInterface $segmentPageService;

    private EntityManagerInterface $entityManager;

    private EventDispatcherInterface $eventDispatcher;

    private FormServiceInterface $formService;

    private GalleryServiceInterface $galleryService;

    private FileServiceInterface $fileService;

    /**
     * SegmentService constructor.
     */
    public function __construct(
        SegmentPageServiceInterface $segmentPageService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        FormServiceInterface $formService,
        GalleryServiceInterface $galleryService,
        FileServiceInterface $fileService
    ) {
        $this->segmentPageService = $segmentPageService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->formService = $formService;
        $this->galleryService = $galleryService;
        $this->fileService = $fileService;
    }

    /**
     * Saves the file in the given segment page at the given position
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
     * @param string|int $slug
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
     * Saves the gallery in the given segment page at the given position
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
     * Saves the form in the given segment page at the given position
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
     * Updates the form in the given segment page at the given position
     */
    public function updateFormSegment(string $formSlug, int $segmentId): int
    {
        return $this->updateSegment($formSlug, $segmentId, 'form');
    }

    /**
     * @param string|int $slug
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
     * Saves the html in the given segment page at the given position
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
     */
    public function deleteSegment(int $id): void
    {
        $segment = $this->get($id);
        $this->entityManager->remove($segment);
        $this->entityManager->flush();
    }

    /**
     * Updates the gallery in the given segment page at the given position
     */
    public function updateGallerySegment(string $gallerySlug, int $segmentId): int
    {
        return $this->updateSegment($gallerySlug, $segmentId, 'gallery');
    }

    /**
     * Updates the file in the given segment page at the given position
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
