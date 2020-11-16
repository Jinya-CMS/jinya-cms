<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Pages;

use Jinya\Entity\Page\Page;
use Jinya\Framework\Events\Common\CancellableEvent;

class PageEvent extends CancellableEvent
{
    public const PRE_SAVE = 'PagePreSave';

    public const POST_SAVE = 'PagePostSave';

    public const PRE_GET = 'PagePreGet';

    public const POST_GET = 'PagePostGet';

    public const PRE_DELETE = 'PagePreDelete';

    public const POST_DELETE = 'PagePostDelete';

    /** @var Page */
    private ?Page $page;

    private string $slug;

    /**
     * PageEvent constructor.
     * @param Page $page
     */
    public function __construct(?Page $page, string $slug)
    {
        $this->page = $page;
        $this->slug = $slug;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Page
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }
}
