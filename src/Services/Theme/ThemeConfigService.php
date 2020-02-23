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
use Symfony\Component\Yaml\Yaml;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use function array_filter;
use function array_replace_recursive;
use function preg_replace;

class ThemeConfigService implements ThemeConfigServiceInterface
{
    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /** @var MediaServiceInterface */
    private MediaServiceInterface $mediaService;

    /** @var ArrayUtilInterface */
    private ArrayUtilInterface $arrayUtils;

    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * ThemeConfigService constructor.
     * @param ThemeServiceInterface $themeService
     * @param MenuServiceInterface $menuService
     * @param EntityManagerInterface $entityManager
     * @param MediaServiceInterface $mediaService
     * @param ArrayUtilInterface $arrayUtils
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ThemeServiceInterface $themeService,
        MenuServiceInterface $menuService,
        EntityManagerInterface $entityManager,
        MediaServiceInterface $mediaService,
        ArrayUtilInterface $arrayUtils,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->themeService = $themeService;
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
        $themeYml = implode(
            DIRECTORY_SEPARATOR,
            [$this->themeService->getThemeDirectory(), $name, ThemeService::THEME_CONFIG_YML]
        );

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
        $handle = fopen($variablesPath, 'rb+');
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

        return sprintf(
            '%s%s%s%s%s',
            $this->themeService->getThemeDirectory(),
            DIRECTORY_SEPARATOR,
            $theme->getName(),
            DIRECTORY_SEPARATOR,
            $stylesBasePath
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getThemeConfig(string $name): array
    {
        $theme = $this->themeService->getTheme($name);

        return Yaml::parse(file_get_contents(implode(
            DIRECTORY_SEPARATOR,
            [$this->themeService->getThemeDirectory(), $theme->getName(), ThemeService::THEME_CONFIG_YML]
        )));
    }

    /**
     * {@inheritdoc}
     */
    public function setVariables(string $name, array $variables): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new ThemeVariablesEvent($name, $variables),
            ThemeVariablesEvent::PRE_SAVE
        );
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getTheme($name);
            $theme->setScssVariables(array_filter($variables));
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(
                new ThemeVariablesEvent($name, $variables),
                ThemeVariablesEvent::POST_SAVE
            );
        }
    }

    /**
     * Resets the given themes configuration
     *
     * @param string $name
     */
    public function resetConfig(string $name): void
    {
        $pre = $this->eventDispatcher->dispatch(new ThemeConfigEvent($name, [], true), ThemeConfigEvent::PRE_RESET);
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getThemeOrNewTheme($name);
            $theme->setConfiguration([]);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new ThemeConfigEvent($name, [], true), ThemeConfigEvent::POST_RESET);
        }
    }

    /**
     * Resets the given themes variables
     *
     * @param string $name
     */
    public function resetVariables(string $name): void
    {
        $pre = $this->eventDispatcher->dispatch(new ThemeVariablesEvent($name, []), ThemeVariablesEvent::PRE_RESET);
        if (!$pre->isCancel()) {
            $theme = $this->themeService->getThemeOrNewTheme($name);
            $theme->setScssVariables([]);
            $this->entityManager->flush();
            $this->eventDispatcher->dispatch(new ThemeVariablesEvent($name, []), ThemeVariablesEvent::POST_RESET);
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

        $this->saveConfig($name, $this->arrayUtils->removeArrayValueByPath($config, $key));
    }

    /**
     * {@inheritdoc}
     */
    public function saveConfig(string $themeName, array $config, bool $override = true): void
    {
        $pre = $this->eventDispatcher->dispatch(
            new ThemeConfigEvent($themeName, $config, $override),
            ThemeConfigEvent::PRE_SAVE
        );

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

            $this->eventDispatcher->dispatch(
                new ThemeConfigEvent($themeName, $config, $override),
                ThemeConfigEvent::POST_SAVE
            );
        }
    }
}
