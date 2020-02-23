<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 09.12.18
 * Time: 20:46
 */

namespace Jinya\Framework\Events\Theme;

use Jinya\Framework\Events\Common\CancellableEvent;

class ThemeLinkEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ThemeLinksPreSave';

    public const POST_SAVE = 'ThemeLinksPostSave';

    /** @var string */
    private string $themeName;

    /** @var string */
    private string $key;

    /** @var string */
    private string $type;

    /** @var string */
    private string $slug;

    /** @var int */
    private int $id;

    /**
     * ThemeLinkEvent constructor.
     * @param string $themeName
     * @param string $key
     * @param string $type
     * @param string $slug
     * @param int $id
     */
    public function __construct(string $themeName, string $key, string $type, string $slug = '', int $id = -1)
    {
        $this->themeName = $themeName;
        $this->key = $key;
        $this->type = $type;
        $this->slug = $slug;
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getThemeName(): string
    {
        return $this->themeName;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
