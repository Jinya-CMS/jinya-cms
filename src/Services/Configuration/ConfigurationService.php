<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 18:03
 */

namespace Jinya\Services\Configuration;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Jinya\Entity\Configuration\Configuration;
use Jinya\Framework\Events\Configuration\ConfigurationEvent;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ConfigurationService implements ConfigurationServiceInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * ConfigurationService constructor.
     * @param EntityManagerInterface $entityManager
     * @param ThemeServiceInterface $themeService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ThemeServiceInterface $themeService,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->entityManager = $entityManager;
        $this->themeService = $themeService;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfig(): Configuration
    {
        try {
            $this->eventDispatcher->dispatch(new ConfigurationEvent(null), ConfigurationEvent::PRE_GET);

            $config = $this->entityManager->getRepository(Configuration::class)->findAll()[0];
        } catch (Exception $exception) {
            $theme = $this->themeService->getDefaultJinyaTheme();
            if (!$this->entityManager->isOpen()) {
                $this->entityManager = EntityManager::create(
                    $this->entityManager->getConnection(),
                    $this->entityManager->getConfiguration()
                );
            }

            $config = new Configuration();
            $this->entityManager->getConnection()->insert('configuration', [
                'current_frontend_theme_id' => $theme->getId(),
                'invalidate_api_key_after' => Configuration::DEFAULT_API_KEY_INVALIDATION,
            ]);
        }

        $this->eventDispatcher->dispatch(new ConfigurationEvent($config), ConfigurationEvent::POST_GET);

        return $config;
    }

    /**
     * {@inheritdoc}
     */
    public function writeConfig(Configuration $configuration): void
    {
        $pre = $this->eventDispatcher->dispatch(new ConfigurationEvent($configuration), ConfigurationEvent::PRE_WRITE);
        if (!$pre->isCancel()) {
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new ConfigurationEvent($configuration), ConfigurationEvent::POST_WRITE);
        }
    }
}
