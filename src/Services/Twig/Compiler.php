<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:22
 */

namespace Jinya\Services\Twig;

use Jinya\Entity\Theme\Theme;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeConfigServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Underscore\Types\Strings;

class Compiler implements CompilerInterface
{
    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /** @var ThemeCompilerServiceInterface */
    private $themeCompilerService;

    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;

    /** @var ThemeServiceInterface */
    private $themeService;

    /** @var \Twig_Environment */
    private $twig;

    /**
     * Compiler constructor.
     * @param ConfigurationServiceInterface $configurationService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ThemeServiceInterface $themeService
     * @param \Twig_Environment $twig
     */
    public function __construct(ConfigurationServiceInterface $configurationService, ThemeCompilerServiceInterface $themeCompilerService, ThemeConfigServiceInterface $themeConfigService, ThemeServiceInterface $themeService, \Twig_Environment $twig)
    {
        $this->configurationService = $configurationService;
        $this->themeCompilerService = $themeCompilerService;
        $this->themeConfigService = $themeConfigService;
        $this->themeService = $themeService;
        $this->twig = $twig;
    }

    /**
     * Compiles the given template with the given context
     *
     * @param string $path
     * @param array $context
     * @return string
     * @throws \Twig_Error_Loader
     * @throws \Twig_Error_Runtime
     * @throws \Twig_Error_Syntax
     */
    public function compile(string $path, array $context): string
    {
        $currentTheme = $this->configurationService->getConfig()->getCurrentTheme();

        if (Strings::find($path, '@Theme')) {
            list($themeViewPath, $parameters) = $this->includeTheme($path, $context, $currentTheme);

            return $this->twig->render($themeViewPath, $parameters);
        }

        return $this->twig->render($path, $context);
    }

    /**
     * @param string $view
     * @param array $parameters
     * @param $theme
     * @return array
     */
    private function includeTheme(string $view, array $parameters, Theme $theme): array
    {
        $isCompiled = $this->themeCompilerService->isCompiled($theme);
        header("Compiled: $isCompiled");
        if (!$isCompiled || getenv('APP_DEBUG')) {
            $this->themeCompilerService->compileTheme($theme);
        }

        $this->themeService->registerThemes();
        $themeViewPath = '@Themes/' . $this->themeConfigService->getThemeNamespace($theme) . str_replace('@Theme', '/', $view);

        $parameters['themeConfig'] = $theme->getConfiguration();
        $this->twig->addGlobal('themeConfig', $theme->getConfiguration());

        $parameters['theme']['active'] = $theme;
        $parameters['theme']['path'] = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR;

        return [$themeViewPath, $parameters];
    }

    /**
     * Adds a global variable to template engine
     *
     * @param string $name
     * @param $value
     */
    public function addGlobal(string $name, $value): void
    {
        $this->twig->addGlobal($name, $value);
    }
}
