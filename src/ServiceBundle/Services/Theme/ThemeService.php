<?php
// TODO: Refactor this, create a separate ThemeCompilerService

/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 05.01.2018
 * Time: 17:14
 */

namespace ServiceBundle\Services\Theme;


use DataBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\UnitOfWork;
use Exception;
use ServiceBundle\Services\Configuration\FrontendConfigurationServiceInterface;
use ServiceBundle\Services\Menu\MenuServiceInterface;
use ServiceBundle\Services\Scss\ScssCompilerServiceInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Yaml\Yaml;
use Twig\Loader\FilesystemLoader;
use const DIRECTORY_SEPARATOR;
use function array_key_exists;
use function explode;
use function fclose;
use function file_get_contents;
use function fopen;
use function is_array;
use function preg_match;
use function preg_replace;

class ThemeService implements ThemeServiceInterface
{
    private const THEME_CONFIG_YML = 'theme.yml';
    private const JINYA_GALLERY_DEFAULT_THEME_NAME = 'jinya_gallery_default_theme';

    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var FrontendConfigurationServiceInterface
     */
    private $frontendConfigurationService;
    /**
     * @var string
     */
    private $themeDirectory;
    /**
     * @var string
     */
    private $kernelProjectDir;
    /**
     * @var FilesystemLoader
     */
    private $twigLoader;
    /**
     * @var MenuServiceInterface
     */
    private $menuService;

    /**
     * ThemeService constructor.
     * @param EntityManager $entityManager
     * @param FrontendConfigurationServiceInterface $frontendConfigurationService
     * @param string $themeDirectory
     * @param string $kernelProjectDir
     * @param FilesystemLoader $twigLoader
     * @param ScssCompilerServiceInterface $scssCompilerService
     * @param MenuServiceInterface $menuService
     */
    public function __construct(EntityManager $entityManager, FrontendConfigurationServiceInterface $frontendConfigurationService, string $themeDirectory, string $kernelProjectDir, FilesystemLoader $twigLoader, MenuServiceInterface $menuService)
    {
        $this->entityManager = $entityManager;
        $this->frontendConfigurationService = $frontendConfigurationService;
        $this->themeDirectory = $themeDirectory;
        $this->kernelProjectDir = $kernelProjectDir;
        $this->twigLoader = $twigLoader;
        $this->menuService = $menuService;
    }

    /**
     * @inheritdoc
     */
    public function syncThemes(): void
    {
        $finder = new Finder();
        $themeDirectory = $this->kernelProjectDir . DIRECTORY_SEPARATOR . $this->themeDirectory;
        $configFiles = $finder->files()
            ->in($themeDirectory)
            ->name(ThemeService::THEME_CONFIG_YML);

        foreach ($configFiles as $configFile) {
            /** @var $configFile SplFileInfo */
            $this->saveTheme($configFile->getContents(), $configFile->getRelativePath());
        }
    }

    /**
     * @inheritdoc
     */
    private function saveTheme(string $configString, string $name)
    {
        $config = Yaml::parse($configString, Yaml::PARSE_OBJECT);
        $theme = $this->getThemeOrNewTheme($name);
        $theme->setName($name);
        $theme->setDisplayName($config['displayName']);
        $theme->setDescription(array_key_exists('description', $config) ? $config['description'] : '');
        if (array_key_exists('defaultConfig', $config)) {
            $themeConfig = $theme->getConfiguration();
            $defaultConfig = $config['defaultConfig'];
            if (is_array($defaultConfig)) {
                $theme->setConfiguration(array_replace_recursive($defaultConfig, $themeConfig));
            }
        }
        $theme->setPreviewImage(array_key_exists('previewImage', $config) ? $config['previewImage'] : '');

        if ($this->entityManager->getUnitOfWork()->getEntityState($theme) === UnitOfWork::STATE_NEW) {
            $this->entityManager->persist($theme);
        }

        $this->entityManager->flush();
    }

    /**
     * @param string $name
     * @return Theme
     */
    private function getThemeOrNewTheme(string $name): Theme
    {
        try {
            $theme = $this->getTheme($name);
        } catch (Exception $e) {
            $theme = new Theme();
        }

        return $theme;
    }

    /**
     * @inheritdoc
     */
    public function getTheme(string $name): Theme
    {
        $repository = $this->entityManager->getRepository(Theme::class);
        return $repository
            ->createQueryBuilder('theme')
            ->where('theme.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @inheritdoc
     */
    public function saveConfig(array $config, string $themeName): void
    {
        $theme = $this->getThemeOrNewTheme($themeName);
        $themeConfig = $this->getThemeConfig($theme);

        if (array_key_exists('defaultConfig', $themeConfig)) {
            $defaultConfig = $themeConfig['defaultConfig'];
            if (is_array($defaultConfig)) {
                $theme->setConfiguration(array_replace_recursive($defaultConfig, $config));
            }
        }
        $this->entityManager->flush();
    }

    /**
     * @inheritdoc
     */
    public function getThemeConfig(string $name): array
    {
        $theme = $this->getTheme($name);

        return Yaml::parse(file_get_contents($this->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML));
    }

    /**
     * @inheritdoc
     */
    public function getThemeDirectory(): string
    {
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . $this->themeDirectory;
    }

    /**
     * @inheritdoc
     */
    public function getAllThemes(): array
    {
        return $this->entityManager->getRepository(Theme::class)->findAll();
    }

    /**
     * @inheritdoc
     */
    public function getDefaultJinyaTheme(): Theme
    {
        return $this->getTheme(ThemeService::JINYA_GALLERY_DEFAULT_THEME_NAME);
    }

    /**
     * @inheritdoc
     */
    public function getThemeNamespace(Theme $theme): string
    {
        return '@Themes/' . $theme->getName();
    }

    /**
     * @inheritdoc
     */
    public function registerThemes(): void
    {
        $this->twigLoader->addPath($this->getThemeDirectory(), 'Themes');
    }

    /**
     * @inheritdoc
     */
    public function getActiveTheme(): Theme
    {
        return $this->frontendConfigurationService->getConfig()->getCurrentTheme();
    }

    /**
     * @inheritdoc
     */
    public function getConfigForm(string $name): array
    {
        return $this->getForms($name)['config'];
    }

    /**
     * Gets the forms for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getForms(string $name): array
    {
        $themeYml = $this->getThemeDirectory() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML;

        $themeData = Yaml::parseFile($themeYml);

        return $themeData['form'];
    }

    /**
     * Gets the variables for the given theme
     *
     * @param string $name
     * @return array
     */
    public function getVariables(string $name): array
    {
        $theme = $this->getTheme($name);
        $stylesPath = $this->getStylesPath($theme);
        $themeConfig = $this->getThemeConfig($theme);

        $variablesPath = $stylesPath . DIRECTORY_SEPARATOR . $themeConfig['styles']['variables']['file'];
        $handle = fopen($variablesPath, "r");
        $variables = [];

        try {
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/^\$.*!default;\s$/', $line)) {
                        $replaced = preg_replace('/ !default;$/', '', $line);
                        $variables[] = explode(':', $replaced);
                    }
                }
            }
        } finally {
            fclose($handle);
        }

        return $variables;
    }

    /**
     * @inheritdoc
     */
    public function getStylesPath(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme);
        $stylesBasePath = 'public/scss/';
        if (array_key_exists('styles_base', $themeConfig)) {
            $stylesBasePath = $themeConfig['styles_base'];
        }

        $stylesPath = $this->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $stylesBasePath;

        return $stylesPath;
    }

    /**
     * @inheritdoc
     */
    public function setVariables(string $name, array $variables): void
    {
        $theme = $this->getTheme($name);
        $theme->setScssVariables(array_filter($variables));
        $this->entityManager->flush($theme);
    }

    /**
     * @inheritdoc
     */
    public function setMenus(string $name, array $menus): void
    {
        $theme = $this->getTheme($name);

        $primaryMenu = $menus['primary'];
        $secondaryMenu = $menus['secondary'];
        $footerMenu = $menus['footer'];

        if ($primaryMenu) {
            $theme->setPrimaryMenu($this->menuService->get($primaryMenu));
        }
        if ($secondaryMenu) {
            $theme->setSecondaryMenu($this->menuService->get($secondaryMenu));
        }
        if ($footerMenu) {
            $theme->setFooterMenu($this->menuService->get($footerMenu));
        }

        $this->entityManager->flush($theme);
    }
}