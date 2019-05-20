<?php

namespace Jinya\Command;

use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use Underscore\Types\Strings;

class CreateThemeCommand extends Command
{
    /** @var SlugServiceInterface */
    private $slugService;

    /** @var string */
    private $themesDir;

    /** @var Environment */
    private $twig;

    /**
     * CreateThemeCommand constructor.
     * @param SlugServiceInterface $slugService
     * @param string $themesDir
     * @param Environment $twig
     */
    public function __construct(SlugServiceInterface $slugService, string $themesDir, Environment $twig)
    {
        parent::__construct();
        $this->slugService = $slugService;
        $this->themesDir = $themesDir;
        $this->twig = $twig;
    }

    protected function configure()
    {
        $this->setName('jinya:dev:theme:create');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $displayName = $helper->ask($input, $output, new Question('<question>Display name:</question> '));
        $identifierName = $this->slugService->generateSlug($displayName);
        $identifierName = $helper->ask(
            $input,
            $output,
            new Question("<question>Identifier:</question> <comment>$identifierName:</comment> ", $identifierName)
        );
        $description = $helper->ask($input, $output, new Question('<question>Description:</question> ', ''));
        $previewImage = $helper->ask($input, $output, new Question('<question>Preview image:</question> ', ''));
        $stylesBase = $helper->ask(
            $input,
            $output,
            new Question('<question>SCSS style directory:</question> <comment>styles:</comment> ', 'styles')
        );
        $scriptsBase = $helper->ask(
            $input,
            $output,
            new Question('<question>JavaScript directory:</question> <comment>scripts:</comment> ', 'scripts')
        );
        $stylesVariablesFile = $helper->ask(
            $input,
            $output,
            new Question(
                '<question>SCSS variables file:</question> <comment>_variables.scss:</comment> ',
                '_variables.scss'
            )
        );
        $stylesFiles = $helper->ask(
            $input,
            $output,
            new Question(
                '<question>SCSS files to compile, comma separated:</question> <comment>frontend.scss:</comment> ',
                'frontend.scss'
            )
        );

        $config = [
            'displayName' => $displayName,
            'description' => $description,
            'previewImage' => $previewImage,
            'styles_base' => $stylesBase,
            'scripts_base' => $scriptsBase,
            'styles' => [
                'variables' => [
                    'file' => $stylesVariablesFile,
                ],
                'files' => Strings::explode(Strings::remove($stylesFiles, ' '), ','),
            ],
        ];

        $configYaml = Yaml::dump($config, 4);
        $output->writeln("Theme $identifierName has the following config");
        $output->writeln($configYaml);

        $dumpData = $helper->ask(
            $input,
            $output,
            new ConfirmationQuestion('<question>Is this correct?</question> <comment>y:</comment> ', 'y', '/^y/i')
        );

        if ($dumpData) {
            $themePath = sprintf('%s/%s', $this->themesDir, $identifierName);

            $fs = new Filesystem();
            $fs->dumpFile(sprintf('%s/%s', $themePath, '/theme.yml'), $dumpData);
            $fs->mkdir([
                "$themePath/$scriptsBase",
                "$themePath/$stylesBase",
                "$themePath/Default",
                "$themePath/Form",
                "$themePath/Gallery",
                "$themePath/Page",
                "$themePath/Profile",
            ]);

            $fs->touch([
                sprintf('%s/Default/index.html.twig', $themePath),
                sprintf('%s/Form/detail.html.twig', $themePath),
                sprintf('%s/Gallery/detail.html.twig', $themePath),
                sprintf('%s/Page/detail.html.twig', $themePath),
                sprintf('%s/Profile/detail.html.twig', $themePath),
            ]);
            $fs->touch(array_map(static function ($item) use ($themePath, $stylesBase) {
                return "$themePath/$stylesBase/$item";
            }, $config['styles']['files']));

            $gulpfile = $this->twig->render('@Jinya/ThemeCreator/Gulpfile.js.twig', ['scriptsPath' => $scriptsBase]);
            $fs->dumpFile(sprintf('%s/Gulpfile.js', $themePath), $gulpfile);

            $addGitRepo = $helper->ask(
                $input,
                $output,
                new ConfirmationQuestion(
                    '<question>Do you want to add a git repo?</question> <comment>n:</comment> ',
                    'n',
                    '/^y/i'
                )
            );

            if ($addGitRepo) {
                $repo = $helper->ask(
                    $input,
                    $output,
                    new Question('<question>Git remote repo:</question> ')
                );
                $output->writeln(passthru("cd $themePath && git init"));
                $output->writeln(passthru("cd $themePath && git remote add origin $repo"));
            }
        }
    }
}
