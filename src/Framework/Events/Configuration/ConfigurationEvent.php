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

    /** @var Configuration */
    private $config;

    /**
     * ConfigurationEvent constructor.
     * @param Configuration $config
     */
    public function __construct(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * @return Configuration
     */
    public function getConfig(): Configuration
    {
        return $this->config;
    }
}