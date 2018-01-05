<?php

namespace FrontendBundle;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FrontendBundle extends Bundle
{
    /**
     * @inheritdoc
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        try {
            $themeService = $container->get('jinya_gallery.services.theme_service');
            $frontendConfigurationService = $container->get('jinya_gallery.services.frontend_configuration_service');

            $themeService->syncThemes();
            $container->set('jinya_gallery.frontend_configuration', $frontendConfigurationService->getConfig());
        } catch (Exception $e) {
        }
    }
}
