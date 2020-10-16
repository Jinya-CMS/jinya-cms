<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 18:56
 */

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use const DIRECTORY_SEPARATOR;

class ThemeSyncService implements ThemeSyncServiceInterface
{
    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var string */
    private string $themeDirectory;

    /**
     * ThemeSyncService constructor.
     */
    public function __construct(
        ThemeServiceInterface $themeService,
        EntityManagerInterface $entityManager,
        string $themeDirectory
    ) {
        $this->themeService = $themeService;
        $this->entityManager = $entityManager;
        $this->themeDirectory = $themeDirectory;
    }

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    public function syncThemes(): void
    {
        $finder = new Finder();
        $absoluteThemeDirectory = $this->themeDirectory;
        $configFiles = $finder->files()
            ->in($absoluteThemeDirectory)
            ->name(ThemeService::THEME_CONFIG_YML);

        foreach ($configFiles as $configFile) {
            /* @var $configFile SplFileInfo */
            $this->saveTheme($configFile->getContents(), $configFile->getRelativePath());
        }
    }

    /**
     * {@inheritdoc}
     * @throws ORMException
     */
    private function saveTheme(string $configString, string $name): void
    {
        $config = Yaml::parse($configString, Yaml::PARSE_OBJECT);
        $theme = $this->themeService->getThemeOrNewTheme($name);
        $theme->setScssVariables($theme->getScssVariables() ?? []);
        $theme->setName($name);
        $theme->setDisplayName($config['displayName']);
        $theme->setDescription(array_key_exists('description', $config) ? $config['description'] : '');
        if (array_key_exists('defaultConfig', $config)) {
            $themeConfig = $theme->getConfiguration();
            $defaultConfig = $config['defaultConfig'];
            if (is_array($defaultConfig)) {
                $theme->setConfiguration(array_replace_recursive($defaultConfig, $themeConfig));
            }
        } else {
            $theme->setConfiguration([]);
        }
        if (array_key_exists('previewImage', $config)) {
            $previewImagePath = implode(DIRECTORY_SEPARATOR, [$this->themeDirectory, $name, $config['previewImage']]);
            $theme->setPreviewImage($previewImagePath);
        }

        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($theme)) {
            if (!$this->entityManager->isOpen()) {
                $this->entityManager = EntityManager::create(
                    $this->entityManager->getConnection(),
                    $this->entityManager->getConfiguration()
                );
            }
            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();
    }
}
