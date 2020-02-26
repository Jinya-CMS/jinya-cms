<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 26.08.18
 * Time: 15:11
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\File;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Framework\Events\Common\CancellableEvent;

class GalleryFilePositionUpdateFileEvent extends CancellableEvent
{
    public const PRE_UPDATE_FILE = 'GalleryFilePositionPreUpdateFile';

    public const POST_UPDATE_FILE = 'GalleryFilePositionPostUpdateFile';

    /** @var GalleryFilePosition */
    private $galleryFilePosition;

    /** @var int */
    private int $positionId;

    /**
     * ArtworkPositionUpdateArtworkEvent constructor.
     * @param File $galleryFilePosition
     * @param int $positionId
     */
    public function __construct(File $galleryFilePosition, int $positionId)
    {
        $this->galleryFilePosition = $galleryFilePosition;
        $this->positionId = $positionId;
    }

    /**
     * @return File
     */
    public function getFile(): File
    {
        return $this->galleryFilePosition;
    }

    /**
     * @return int
     */
    public function getPositionId(): int
    {
        return $this->positionId;
    }
}
