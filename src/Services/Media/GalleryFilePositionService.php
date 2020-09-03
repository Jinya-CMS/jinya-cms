<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.11.2017
 * Time: 18:09
 */

namespace Jinya\Services\Media;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Framework\Events\Media\GalleryFilePositionDeleteEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionUpdateEvent;
use Jinya\Framework\Events\Media\GalleryFilePositionUpdateFileEvent;
use Jinya\Framework\Events\Media\RearrangeEvent;
use Jinya\Services\Base\ArrangementServiceTrait;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class GalleryFilePositionService implements GalleryFilePositionServiceInterface
{
    use ArrangementServiceTrait;

    /** @var GalleryServiceInterface */
    private GalleryServiceInterface $galleryService;

    /** @var FileServiceInterface */
    private FileServiceInterface $fileService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * ArtworkPositionService constructor.
     */
    public function __construct(
        GalleryServiceInterface $galleryService,
        FileServiceInterface $fileService,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->galleryService = $galleryService;
        $this->fileService = $fileService;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Saves the artwork in the given gallery at the given position
     */
    public function savePosition(int $fileId, int $galleryId, int $position): int
    {
        $gallery = $this->galleryService->get($galleryId);
        $file = $this->fileService->get($fileId);

        $galleryFilePosition = new GalleryFilePosition();
        $galleryFilePosition->setFile($file);
        $galleryFilePosition->setGallery($gallery);

        $pre = $this->eventDispatcher->dispatch(
            new GalleryFilePositionEvent($galleryFilePosition, -1),
            GalleryFilePositionEvent::PRE_SAVE
        );

        if (!$pre->isCancel()) {
            $this->rearrangeFiles(-1, $position, $galleryFilePosition, $gallery);

            $this->eventDispatcher->dispatch(
                new GalleryFilePositionEvent($galleryFilePosition, $galleryFilePosition->getId()),
                GalleryFilePositionEvent::POST_SAVE
            );
        }

        return $galleryFilePosition->getId();
    }

    private function rearrangeFiles(
        int $oldPosition,
        int $newPosition,
        GalleryFilePosition $galleryFilePosition,
        Gallery $gallery
    ): void {
        $pre = $this->eventDispatcher->dispatch(
            new RearrangeEvent($gallery, $galleryFilePosition, $oldPosition, $newPosition),
            RearrangeEvent::PRE_REARRANGE
        );

        if (!$pre->isCancel()) {
            $positions = $gallery->getFiles()->toArray();
            $positions = $this->rearrange($positions, $oldPosition, $newPosition, $galleryFilePosition);

            $gallery->setFiles(new ArrayCollection($positions));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                new RearrangeEvent($gallery, $galleryFilePosition, $oldPosition, $newPosition),
                RearrangeEvent::POST_REARRANGE
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updatePosition(
        string $gallerySlug,
        int $galleryFilePositionId,
        int $oldPosition,
        int $newPosition
    ): void {
        $pre = $this->eventDispatcher->dispatch(
            new GalleryFilePositionUpdateEvent($gallerySlug, $galleryFilePositionId, $oldPosition, $newPosition),
            GalleryFilePositionUpdateEvent::PRE_UPDATE
        );

        if (!$pre->isCancel()) {
            $gallery = $this->galleryService->get($gallerySlug);

            $file = $this->entityManager->getRepository(GalleryFilePosition::class)->find($galleryFilePositionId);

            $this->rearrangeFiles($oldPosition, $newPosition, $file, $gallery);
            $this->eventDispatcher->dispatch(
                new GalleryFilePositionUpdateEvent($gallerySlug, $galleryFilePositionId, $oldPosition, $newPosition),
                GalleryFilePositionUpdateEvent::POST_UPDATE
            );
        }
    }

    /**
     * Deletes the given artwork position
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function deletePosition(int $id): void
    {
        $gallery = $this->entityManager
            ->createQueryBuilder()
            ->select('gallery_file_position')
            ->from(GalleryFilePosition::class, 'gallery_file_position')
            ->where('gallery_file_position.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
        $pre = $this->eventDispatcher->dispatch(
            new GalleryFilePositionDeleteEvent($gallery, $id),
            GalleryFilePositionDeleteEvent::PRE_DELETE
        );

        if (!$pre->isCancel()) {
            $this->entityManager
                ->createQueryBuilder()
                ->delete(GalleryFilePosition::class, 'e')
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();
            $this->eventDispatcher->dispatch(
                new GalleryFilePositionDeleteEvent($gallery, $id),
                GalleryFilePositionDeleteEvent::POST_DELETE
            );
        }
    }

    /**
     * Gets the artwork position for the given id
     */
    public function getPosition(int $id): GalleryFilePosition
    {
        $this->eventDispatcher->dispatch(new GalleryFilePositionEvent(null, $id), GalleryFilePositionEvent::PRE_GET);
        $position = $this->entityManager->find(GalleryFilePosition::class, $id);
        $this->eventDispatcher->dispatch(
            new GalleryFilePositionEvent($position, $id),
            GalleryFilePositionEvent::POST_GET
        );

        return $position;
    }

    /**
     * Sets the artwork of the given artwork position to the new slug
     */
    public function updateFile(int $id, int $fileId): void
    {
        $file = $this->fileService->get($fileId);
        $pre = $this->eventDispatcher->dispatch(
            new GalleryFilePositionUpdateFileEvent($file, $id),
            GalleryFilePositionUpdateFileEvent::PRE_UPDATE_FILE
        );

        if (!$pre->isCancel()) {
            $this->entityManager
                ->createQueryBuilder()
                ->update(GalleryFilePosition::class, 'e')
                ->set('e.file', $file->getId())
                ->where('e.id = :id')
                ->setParameter('id', $id)
                ->getQuery()
                ->execute();

            $this->eventDispatcher->dispatch(
                new GalleryFilePositionUpdateFileEvent($file, $id),
                GalleryFilePositionUpdateFileEvent::POST_UPDATE_FILE
            );
        }
    }
}
