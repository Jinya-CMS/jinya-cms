<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 18:02
 */

namespace Jinya\Services\Configuration;

use Jinya\Entity\Configuration;

interface ConfigurationServiceInterface
{
    /**
     * Gets the frontend configuration
     *
     * @return Configuration
     */
    public function getConfig(): Configuration;

    /**
     * Saves the frontend configuration
     *
     * @param Configuration $configuration
     */
    public function writeConfig(Configuration $configuration): void;
}
