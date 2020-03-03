<?php

namespace Jinya\Command;

use Jinya\Services\Theme\ThemeCompilerServiceInterface;
use Jinya\Services\Theme\ThemeServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileThemesCommand extends Command
{
    /** @var ThemeServiceInterface */
    private ThemeServiceInterface $themeService;

    /** @var ThemeCompilerServiceInterface */
    private ThemeCompilerServiceInterface $themeCompilerService;

    /**
     * CompileThemesCommand constructor.
     * @param ThemeServiceInterface $themeService
     * @param ThemeCompilerServiceInterface $themeCompilerService
     */
    public function __construct(
        ThemeServiceInterface $themeService,
        ThemeCompilerServiceInterface $themeCompilerService
    ) {
        parent::__construct();
        $this->themeService = $themeService;
        $this->themeCompilerService = $themeCompilerService;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jinya:themes:compile')
            ->setDescription('Compiles all themes');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $themes = $this->themeService->getAllThemes();
        foreach ($themes as $theme) {
            $output->writeln('Compiling theme ' . $theme->getName());
            $this->themeCompilerService->compileTheme($theme);
            $output->writeln('Compiled theme ' . $theme->getName());
        }

        $output->writeln('Compiled all themes');

        return 0;
    }
}
