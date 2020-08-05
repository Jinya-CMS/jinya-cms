<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 23.08.18
 * Time: 19:01
 */

namespace Jinya\Framework\Events\Configuration;

use Jinya\Entity\Configuration\Configuration;
use Jinya\Framework\Events\Common\CancellableEvent;

class ConfigurationEvent extends CancellableEvent
{
    public const PRE_GET = 'ConfigurationPreGet';

    public const POST_GET = 'ConfigurationPostGet';

    public const PRE_WRITE = 'ConfigurationPreWrite';

    public const POST_WRITE = 'ConfigurationPostWrite';

    /** @var ?Configuration */
    private ?Configuration $config;

    /**
     * ConfigurationEvent constructor.
     */
    public function __construct(?Configuration $config)
    {
        $this->config = $config;
    }

    public function getConfig(): ?Configuration
    {
        return $this->config;
    }
}
