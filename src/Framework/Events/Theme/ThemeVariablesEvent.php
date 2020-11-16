<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 25.08.18
 * Time: 00:50
 */

namespace Jinya\Framework\Events\Theme;

use Jinya\Framework\Events\Common\CancellableEvent;

class ThemeVariablesEvent extends CancellableEvent
{
    public const PRE_SAVE = 'ThemeVariablesPreSave';

    public const POST_SAVE = 'ThemeVariablesPostSave';

    public const PRE_RESET = 'ThemeVariablesPreReset';

    public const POST_RESET = 'ThemeVariablesPostReset';

    private string $themeName;

    private array $variables;

    /**
     * ThemeVariablesEvent constructor.
     */
    public function __construct(string $themeName, array $variable)
    {
        $this->themeName = $themeName;
        $this->variables = $variable;
    }

    public function getThemeName(): string
    {
        return $this->themeName;
    }

    public function getVariables(): array
    {
        return $this->variables;
    }
}
