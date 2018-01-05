<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 18:03
 */

namespace DataBundle\Services\Configuration;


use DataBundle\Entity\FrontendConfiguration;
use DataBundle\Services\Theme\ThemeServiceInterface;
use Doctrine\ORM\EntityManager;
use Exception;

class FrontendConfigurationService implements FrontendConfigurationServiceInterface
{

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var ThemeServiceInterface
     */
    private $themeService;

    /**
     * FrontendConfigurationService constructor.
     * @param EntityManager $entityManager
     * @param ThemeServiceInterface $themeService
     */
    public function __construct(EntityManager $entityManager, ThemeServiceInterface $themeService)
    {
        $this->entityManager = $entityManager;
        $this->themeService = $themeService;
    }

    /**
     * @inheritdoc
     */
    public function getConfig(): FrontendConfiguration
    {
        try {
            return $this->entityManager->getRepository(FrontendConfiguration::class)->findAll()[0];
        } catch (Exception $exception) {
            $config = new FrontendConfiguration();
            $config->setCurrentTheme($this->themeService->getDefaultJinyaTheme());

            $this->entityManager->persist($config);
            $this->entityManager->flush();

            return $config;
        }
    }

    /**
     * @inheritdoc
     */
    public function writeConfig(FrontendConfiguration $configuration): void
    {
        $this->entityManager->flush($configuration);
    }
}