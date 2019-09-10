<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Media;

use Jinya\Entity\Media\File;
use Jinya\Framework\Events\Common\CancellableEvent;

class FileEvent extends CancellableEvent
{
    public const PRE_SAVE = 'FilePreSave';

    public const POST_SAVE = 'FilePostSave';

    public const PRE_GET = 'FilePreGet';

    public const POST_GET = 'FilePostGet';

    public const PRE_DELETE = 'FilePreDelete';

    public const POST_DELETE = 'FilePostDelete';

    /** @var File */
    private $file;

    /** @var int */
    private $id;

    /**
     * FileEvent constructor.
     * @param File $file
     * @param int|null $id
     */
    public function __construct(?File $file, ?int $id)
    {
        $this->file = $file;
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
     * @return File
     */
    public function getFile(): ?File
    {
        return $this->file;
    }
}
