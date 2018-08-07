<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 07.08.18
 * Time: 21:53
 */

namespace Jinya\Command;

use Jinya\Services\Cache\CacheBuilderInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CompileCacheCommand extends ContainerAwareCommand
{
    /** @var CacheBuilderInterface */
    private $cacheBuilder;

    /**
     * CompileCacheCommand constructor.
     * @param CacheBuilderInterface $cacheBuilder
     */
    public function __construct(CacheBuilderInterface $cacheBuilder)
    {
        parent::__construct();
        $this->cacheBuilder = $cacheBuilder;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jinya:cache:compile')
            ->setDescription('Compiles the cache');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Compiling all available routes in current theme');
        $this->cacheBuilder->buildCache();
        $output->writeln('Compiled all pages');
    }
}