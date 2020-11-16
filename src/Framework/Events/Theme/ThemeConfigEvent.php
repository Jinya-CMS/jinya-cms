<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 00:50
 */

namespace Jinya\Framework\Events\Theme;

use Jinya\Framework\Events\Common\CancellableEvent;

class ThemeConfigEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ThemeConfigPreSave';

    public const POST_SAVE = 'ThemeConfigPostSave';

    public const PRE_RESET = 'ThemeConfigPreReset';

    public const POST_RESET = 'ThemeConfigPostReset';

    private string $themeName;

    private array $config;

    private bool $override;

    /**
     * ThemeConfigEvent constructor.
     */
    public function __construct(string $themeName, array $config, bool $override)
    {
        $this->themeName = $themeName;
        $this->config = $config;
        $this->override = $override;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function isOverride(): bool
    {
        return $this->override;
    }
}
