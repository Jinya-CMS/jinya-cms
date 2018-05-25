<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 18:03.
 */

namespace Jinya\Services\Configuration;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Jinya\Entity\Configuration;
use Jinya\Services\Theme\ThemeServiceInterface;

class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ThemeServiceInterface
     */
    private $themeService;

    /**
     * FrontendConfigurationService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ThemeServiceInterface  $themeService
     */
    public function __construct(EntityManagerInterface $entityManager, ThemeServiceInterface $themeService)
    {
        $this->entityManager = $entityManager;
        $this->themeService = $themeService;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): Configuration
    {
        try {
            return $this->entityManager->getRepository(Configuration::class)->findAll()[0];
        } catch (Exception $exception) {
            $config = new Configuration();
            $config->setCurrentTheme($this->themeService->getDefaultJinyaTheme());

            $this->entityManager->persist($config);
            $this->entityManager->flush();

            return $config;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function writeConfig(Configuration $configuration): void
    {
        $this->entityManager->flush();
    }
}
