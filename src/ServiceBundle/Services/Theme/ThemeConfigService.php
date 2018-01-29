<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:11
 */

namespace ServiceBundle\Services\Theme;


use DataBundle\Entity\Theme;
use Doctrine\ORM\EntityManager;
use ServiceBundle\Services\Menu\MenuServiceInterface;
use Symfony\Component\Yaml\Yaml;

class ThemeConfigService implements ThemeConfigServiceInterface
{

    /** @var ThemeServiceInterface */
    private $themeService;
    /** @var MenuServiceInterface */
    private $menuService;
    /** @var EntityManager */
    private $entityManager;
    /** @var string */
    private $themeDirectory;
    /** @var string */
    private $kernelProjectDir;

    /**
     * ThemeConfigService constructor.
     * @param ThemeServiceInterface $themeService
     * @param MenuServiceInterface $menuService
     * @param EntityManager $entityManager
     * @param string $themeDirectory
     * @param string $kernelProjectDir
     */
    public function __construct(ThemeServiceInterface $themeService, MenuServiceInterface $menuService, EntityManager $entityManager, string $themeDirectory, string $kernelProjectDir)
    {
        $this->themeService = $themeService;
        $this->menuService = $menuService;
        $this->entityManager = $entityManager;
        $this->themeDirectory = $themeDirectory;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @inheritdoc
     */
    public function saveConfig(array $config, string $themeName): void
    {
        $theme = $this->themeService->getThemeOrNewTheme($themeName);
        $themeConfig = $this->getThemeConfig($theme->getName());

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
        $theme = $this->themeService->getTheme($name);

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
    public function getThemeNamespace(Theme $theme): string
    {
        return '@Themes/' . $theme->getName();
    }

    /**
     * @inheritdoc
     */
    public function getConfigForm(string $name): array
    {
        return $this->getForms($name)['config'];
    }

    /**
     * @inheritdoc
     */
    public function getForms(string $name): array
    {
        $themeYml = $this->getThemeDirectory() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML;

        $themeData = Yaml::parseFile($themeYml);

        return $themeData['form'];
    }

    /**
     * @inheritdoc
     */
    public function getVariables(string $name): array
    {
        $theme = $this->themeService->getTheme($name);
        $stylesPath = $this->getStylesPath($theme);
        $themeConfig = $this->getThemeConfig($theme->getName());

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
        $themeConfig = $this->getThemeConfig($theme->getName());
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
        $theme = $this->themeService->getTheme($name);
        $theme->setScssVariables(array_filter($variables));
        $this->entityManager->flush($theme);
    }

    /**
     * @inheritdoc
     */
    public function setMenus(string $name, array $menus): void
    {
        $theme = $this->themeService->getTheme($name);

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