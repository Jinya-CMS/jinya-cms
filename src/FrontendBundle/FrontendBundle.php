<?php

namespace FrontendBundle;

use Exception;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontendBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function boot()
    {
        try {
            $themeService = $this->container->get('jinya_gallery.services.theme_service');
            $frontendConfigurationService = $this->container->get('jinya_gallery.services.frontend_configuration_service');

            $themeService->syncThemes();
            $this->container->set('jinya_gallery.frontend_configuration', $frontendConfigurationService->getConfig());
        } catch (Exception $e) {
            $logger = $this->container->get('logger');
            $logger->error('Error updating themes');
            $logger->error($e->getMessage());
            $logger->error($e->getTraceAsString());
        }
    }
}
