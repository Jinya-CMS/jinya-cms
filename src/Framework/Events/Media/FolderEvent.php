<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\Folder;
use Jinya\Framework\Events\Common\CancellableEvent;

class FolderEvent extends CancellableEvent
{
    public const PRE_SAVE = 'FolderPreSave';

    public const POST_SAVE = 'FolderPostSave';

    public const PRE_GET = 'FolderPreGet';

    public const POST_GET = 'FolderPostGet';

    public const PRE_DELETE = 'FolderPreDelete';

    public const POST_DELETE = 'FolderPostDelete';

    /** @var Folder */
    private $folder;

    /** @var int */
    private $id;

    /**
     * FolderEvent constructor.
     * @param Folder $folder
     * @param int|null $id
     */
    public function __construct(?Folder $folder, ?int $id)
    {
        $this->folder = $folder;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Folder
     */
    public function getFolder(): ?Folder
    {
        return $this->folder;
    }
}
