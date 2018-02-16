<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.01.2018
 * Time: 07:55
 */

namespace Jinya\Services\Theme;


use Jinya\Entity\Theme;
use Patchwork\JSqueeze;
use Jinya\Services\Scss\ScssCompilerServiceInterface;
use Symfony\Component\Filesystem\Filesystem;

class ThemeCompilerService implements ThemeCompilerServiceInterface
{
    private const THEME_COMPILATION_STATE = 'theme.compilation';

    /** @var ThemeConfigServiceInterface */
    private $themeConfigService;
    /** @var ThemeServiceInterface */
    private $themeService;
    /** @var ScssCompilerServiceInterface */
    private $scssCompilerService;
    /** @var string */
    private $kernelProjectDir;

    /**
     * ThemeCompilerService constructor.
     * @param ThemeConfigServiceInterface $themeConfigService
     * @param ScssCompilerServiceInterface $scssCompilerService
     * @param string $kernelProjectDir
     */
    public function __construct(ThemeConfigServiceInterface $themeConfigService, ScssCompilerServiceInterface $scssCompilerService, string $kernelProjectDir)
    {
        $this->themeConfigService = $themeConfigService;
        $this->scssCompilerService = $scssCompilerService;
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * Compiles the scss and javascript of the given @see Theme
     *
     * @param Theme $theme
     */
    public function compileTheme(Theme $theme): void
    {
        $this->compileStyles($theme);
        $this->concatScripts($theme);
    }

    /**
     * @param Theme $theme
     */
    private function compileStyles(Theme $theme): void
    {
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';

        $fs = new Filesystem();
        $variables = $theme->getScssVariables();

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $result = $this->scssCompilerService->compileScss($scssCode, $this->themeConfigService->getStylesPath($theme), $variables);
                $webStylesPath = $webStylesBasePath . str_replace('scss', 'css', $style);
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $fs->dumpFile($webStylesPath, $result);
                $fs->dumpFile($compilationCheckPath, md5($scssCode));
            }
        }

        $fs->dumpFile($this->getScssVariablesCompilationCheckPath($theme), implode($variables));
    }

    /**
     * @param string $style
     * @param Theme $theme
     * @return string
     */
    private function getScssCodeForStyle(string $style, Theme $theme): string
    {
        $stylesPath = $this->themeConfigService->getStylesPath($theme);
        $scssCode = file_get_contents($stylesPath . '/' . $style);

        return $scssCode;
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathStyles(Theme $theme, string $filename): string
    {
        $webScriptsBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';
        $compilationCheckPath = $webScriptsBasePath . ThemeCompilerService::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();

        return $compilationCheckPath;
    }

    private function getScssVariablesCompilationCheckPath(Theme $theme)
    {
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/styles/';

        return $webStylesBasePath . 'variables';
    }

    /**
     * @param Theme $theme
     */
    private function concatScripts(Theme $theme): void
    {
        $jsQueeze = new JSqueeze();
        $source = $this->getJavaScriptSource($theme);

        $code = $jsQueeze->squeeze($source);

        $webScriptsBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/scripts/';

        $fs = new Filesystem();
        $fs->dumpFile($webScriptsBasePath . 'scripts.js', $code);
        $fs->dumpFile($this->getCompilationCheckPathScripts($theme, 'scripts.js'), md5($source));
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getJavaScriptSource(Theme $theme): string
    {
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $scriptsBasePath = $this->getScriptsPath($theme);

        $source = '';

        if (!empty($themeConfig['scripts'])) {
            foreach ($themeConfig['scripts'] as $script) {
                $source .= file_get_contents($scriptsBasePath . DIRECTORY_SEPARATOR . $script) . "\n";
            }
        }

        return $source;
    }

    /**
     * @param Theme $theme
     * @return string
     */
    private function getScriptsPath(Theme $theme): string
    {
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $scriptsBasePath = 'public/javascripts/';
        if (array_key_exists('scripts_base', $themeConfig)) {
            $scriptsBasePath = $themeConfig['scripts_base'];
        }

        $scriptsPath = $this->themeService->getThemeDirectory() . DIRECTORY_SEPARATOR . $theme->getName() . DIRECTORY_SEPARATOR . $scriptsBasePath;

        return $scriptsPath;
    }

    /**
     * @param Theme $theme
     * @param string $filename
     * @return string
     */
    private function getCompilationCheckPathScripts(Theme $theme, string $filename): string
    {
        $webStylesBasePath = $this->kernelProjectDir . '/web/public/' . $theme->getName() . '/scripts/';
        $compilationCheckPath = $webStylesBasePath . ThemeCompilerService::THEME_COMPILATION_STATE . '.' . $filename . '.' . $theme->getName();
        return $compilationCheckPath;
    }

    /**
     * @inheritdoc
     */
    public function isCompiled(Theme $theme): bool
    {
        return $this->isStylesCompiled($theme) && $this->isScriptsCompiled($theme);
    }

    /**
     * @param Theme $theme
     * @return bool
     */
    private function isStylesCompiled(Theme $theme): bool
    {
        $themeConfig = $this->themeConfigService->getThemeConfig($theme->getName());
        $fs = new Filesystem();

        $variables = $theme->getScssVariables();
        $isCompiled = $fs->exists($this->getScssVariablesCompilationCheckPath($theme)) && implode($variables) === file_get_contents($this->getScssVariablesCompilationCheckPath($theme));

        if ($themeConfig['styles']['files']) {
            foreach ($themeConfig['styles']['files'] as $style) {
                $scssCode = $this->getScssCodeForStyle($style, $theme);
                $compilationCheckPath = $this->getCompilationCheckPathStyles($theme, $style);

                $isCompiled &= $fs->exists($compilationCheckPath) && strcmp(file_get_contents($compilationCheckPath), md5($scssCode)) == 0;
            }
        }

        return $isCompiled;
    }

    /**
     * @param Theme $theme
     * @return bool
     */
    private function isScriptsCompiled(Theme $theme): bool
    {
        $fs = new Filesystem();
        $source = md5($this->getJavaScriptSource($theme));
        $scripts = $this->getCompilationCheckPathScripts($theme, 'scripts.js');

        return $fs->exists($scripts) && strcmp($source, file_get_contents($scripts)) === 0;
    }
}