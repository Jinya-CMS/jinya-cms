<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 08:11
 */

namespace Jinya\Services\Theme;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Components\Arrays\ArrayUtilInterface;
use Jinya\Entity\Theme\Theme;
use Jinya\Framework\Events\Theme\ThemeConfigEvent;
use Jinya\Framework\Events\Theme\ThemeVariablesEvent;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Yaml\Yaml;
use function array_filter;
use function array_replace_recursive;
use function preg_replace;

class ThemeConfigService implements ThemeConfigServiceInterface
{
    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var MenuServiceInterface */
    private $menuService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MediaServiceInterface */
    private $mediaService;

    /** @var ArrayUtilInterface */
    private $arrayUtils;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * ThemeConfigService constructor.
     * @param ThemeServiceInterface $themeService
     * @param MenuServiceInterface $menuService
     * @param EntityManagerInterface $entityManager
     * @param MediaServiceInterface $mediaService
     * @param ArrayUtilInterface $arrayUtils
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(ThemeServiceInterface $themeService, MenuServiceInterface $menuService, EntityManagerInterface $entityManager, MediaServiceInterface $mediaService, ArrayUtilInterface $arrayUtils, EventDispatcherInterface $eventDispatcher)
    {
        $this->themeService = $themeService;
        $this->menuService = $menuService;
        $this->entityManager = $entityManager;
        $this->mediaService = $mediaService;
        $this->arrayUtils = $arrayUtils;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeNamespace(Theme $theme): string
    {
        return $theme->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigForm(string $name): array
    {
        return $this->getForms($name)['config'];
    }

    /**
     * {@inheritdoc}
     */
    public function getForms(string $name): array
    {
        $themeYml = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $name . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML;

        $themeData = Yaml::parseFile($themeYml);

        return $themeData['form'];
    }

    /**
     * {@inheritdoc}
     */
    public function getVariablesForm(string $name): array
    {
        $theme = $this->themeService->getTheme($name);
        $stylesPath = $this->getStylesPath($theme);
        $themeConfig = $this->getThemeConfig($theme->getName());

        $variablesPath = $stylesPath . DIRECTORY_SEPARATOR . $themeConfig['styles']['variables']['file'];
        $handle = fopen($variablesPath, 'r');
        $variables = [];

        try {
            if ($handle) {
                while (false !== ($line = fgets($handle))) {
                    if (preg_match('/^\$.*!default;\s$/', $line)) {
                        $replaced = preg_replace('/ !default;$/', '', $line);
                        $exploded = explode(':', $replaced);
                        $variables[$exploded[0]] = preg_replace('/^\s|\s$/', '', $exploded[1]);
                    }
                }
            }
        } finally {
            fclose($handle);
        }

        return $variables;
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesPath(Theme $theme): string
    {
        $themeConfig = $this->getThemeConfig($theme->getName());
        $stylesBasePath = 'public/scss/';
        if (array_key_exists('styles_base', $themeConfig)) {
            $stylesBasePath = $themeConfig['styles_base'];
        }

        return $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $stylesBasePath;
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeConfig(string $name): array
    {
        $theme = $this->themeService->getTheme($name);

        return Yaml::parse(file_get_contents($this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . ThemeService::THEME_CONFIG_YML));
    }

    /**
     * {@inheritdoc}
     */
    public function setVariables(string $name, array $variables): void
    {
        $pre = $this->eventDispatcher->dispatch(ThemeVariablesEvent::PRE_SAVE, new ThemeVariablesEvent($name, $variables));
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getTheme($name);
            $theme->setScssVariables(array_filter($variables));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(ThemeVariablesEvent::POST_SAVE, new ThemeVariablesEvent($name, $variables));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMenus(string $name, array $menus): void
    {
        $theme = $this->themeService->getTheme($name);

        if (array_key_exists('primary', $menus)) {
            $primaryMenu = $menus['primary'];

            if ($primaryMenu) {
                $theme->setPrimaryMenu('unset' === $primaryMenu ? null : $this->menuService->get($primaryMenu));
            }
        }
        if (array_key_exists('secondary', $menus)) {
            $secondaryMenu = $menus['secondary'];

            if ($secondaryMenu) {
                $theme->setSecondaryMenu('unset' === $secondaryMenu ? null : $this->menuService->get($secondaryMenu));
            }
        }
        if (array_key_exists('footer', $menus)) {
            $footerMenu = $menus['footer'];

            if ($footerMenu) {
                $theme->setFooterMenu('unset' === $footerMenu ? null : $this->menuService->get($footerMenu));
            }
        }

        $this->entityManager->flush();
    }

    /**
     * Resets the given themes configuration
     *
     * @param string $name
     */
    public function resetConfig(string $name): void
    {
        $pre = $this->eventDispatcher->dispatch(ThemeConfigEvent::PRE_RESET, new ThemeConfigEvent($name, [], true));
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getThemeOrNewTheme($name);
            $theme->setConfiguration([]);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(ThemeConfigEvent::POST_RESET, new ThemeConfigEvent($name, [], true));
        }
    }

    /**
     * Resets the given themes variables
     *
     * @param string $name
     */
    public function resetVariables(string $name): void
    {
        $pre = $this->eventDispatcher->dispatch(ThemeVariablesEvent::PRE_RESET, new ThemeVariablesEvent($name, []));
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getThemeOrNewTheme($name);
            $theme->setScssVariables([]);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(ThemeVariablesEvent::POST_RESET, new ThemeVariablesEvent($name, []));
        }
    }

    /**
     * Removes the given file
     *
     * @param string $name
     * @param string $key
     */
    public function removeFile(string $name, string $key): void
    {
        $theme = $this->themeService->getThemeOrNewTheme($name);
        $config = $theme->getConfiguration();
        $file = $this->arrayUtils->getArrayValueByPath($config, $key);

        @$this->mediaService->deleteMedia($file);

        $this->saveConfig($name, $this->arrayUtils->removeArrayValueByPath($config, $key), true);
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfig(string $themeName, array $config, bool $override = true): void
    {
        $pre = $this->eventDispatcher->dispatch(ThemeConfigEvent::PRE_SAVE, new ThemeConfigEvent($themeName, $config, $override));
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getThemeOrNewTheme($themeName);
            $themeConfig = $this->getThemeConfig($theme->getName());

            if ($override) {
                $targetConfig = $this->arrayUtils->removeEmptyRecursive($config);
            } else {
                $targetConfig = array_replace_recursive($theme->getConfiguration(), $config);
                $targetConfig = $this->arrayUtils->removeEmptyRecursive($targetConfig);
            }

            if (array_key_exists('defaultConfig', $themeConfig)) {
                $defaultConfig = $themeConfig['defaultConfig'];
                if (is_array($defaultConfig)) {
                    $theme->setConfiguration(array_replace_recursive($defaultConfig, $targetConfig));
                }
            }
            $this->entityManager->flush();

            $this->eventDispatcher->dispatch(ThemeConfigEvent::POST_SAVE, new ThemeConfigEvent($themeName, $config, $override));
        }
    }
}
