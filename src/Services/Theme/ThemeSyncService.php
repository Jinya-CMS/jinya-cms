<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 18:56
 */

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use const DIRECTORY_SEPARATOR;

class ThemeSyncService implements ThemeSyncServiceInterface
{
    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $kernelProjectDir;

    /** @var string */
    private $themeDirectory;

    /**
     * ThemeSyncService constructor.
     * @param ThemeServiceInterface $themeService
     * @param EntityManagerInterface $entityManager
     * @param string $kernelProjectDir
     * @param string $themeDirectory
     */
    public function __construct(ThemeServiceInterface $themeService, EntityManagerInterface $entityManager, string $kernelProjectDir, string $themeDirectory)
    {
        $this->themeService = $themeService;
        $this->entityManager = $entityManager;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->themeDirectory = $themeDirectory;
    }

    /**
     * {@inheritdoc}
     */
    public function syncThemes(): void
    {
        $finder = new Finder();
        $themeDirectory = $this->kernelProjectDir.DIRECTORY_SEPARATOR.$this->themeDirectory;
        $configFiles = $finder->files()
            ->in($themeDirectory)
            ->name(ThemeService::THEME_CONFIG_YML);

        foreach ($configFiles as $configFile) {
            /* @var $configFile SplFileInfo */
            $this->saveTheme($configFile->getContents(), $configFile->getRelativePath());
        }
    }

    /**
     * {@inheritdoc}
     */
    private function saveTheme(string $configString, string $name)
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
            $previewImagePath = $this->kernelProjectDir.DIRECTORY_SEPARATOR.$this->themeDirectory.DIRECTORY_SEPARATOR.$name.DIRECTORY_SEPARATOR.$config['previewImage'];
            $theme->setPreviewImage($previewImagePath);
        }

        if (UnitOfWork::STATE_NEW === $this->entityManager->getUnitOfWork()->getEntityState($theme)) {
            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();
    }
}
