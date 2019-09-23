<?php

/** @noinspection HtmlUnknownTag */

namespace Jinya\Command\Import;

use Iterator;
use Jinya\Entity\Media\File;
use Jinya\Framework\Security\AuthenticatedCommand;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Users\UserServiceInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Throwable;

class BulkImportFiles extends AuthenticatedCommand
{
    /** @var FileServiceInterface */
    private $fileService;

    /** @var MediaServiceInterface */
    private $mediaService;

    /**
     * BulkImportFiles constructor.
     * @param FileServiceInterface $fileService
     * @param MediaServiceInterface $mediaService
     * @param UserServiceInterface $userService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        FileServiceInterface $fileService,
        MediaServiceInterface $mediaService,
        UserServiceInterface $userService,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($tokenStorage, $userService);
        $this->fileService = $fileService;
        $this->mediaService = $mediaService;
    }

    protected function authenticatedExecute(InputInterface $input, OutputInterface $output): void
    {
        $directory = $input->getArgument('directory');
        $recursive = $input->getOption('recursive');
        $stopOnError = $input->getOption('stop-on-error');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% -- %message%');

        [$files, $count] = $this->scanDirectory($directory, $recursive);
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('custom');
        $progressBar->setMessage('Start file import');
        $progressBar->start();
        $importResult = $this->importFiles($files, $stopOnError, $progressBar);
        $progressBar->finish();
        $this->formatFileOutput($importResult, $output);
    }

    private function scanDirectory(string $directory, bool $recursive): array
    {
        $finder = new Finder();
        $finder
            ->files()
            ->ignoreDotFiles(true)
            ->ignoreVCS(true)
            ->ignoreUnreadableDirs()
            ->sortByName();

        if (!$recursive) {
            $finder->depth(0);
        }

        return [
            $finder
                ->in($directory)
                ->getIterator(),
            $finder
                ->in($directory)
                ->count(),
        ];
    }

    private function importFiles(Iterator $files, bool $stopOnError, ProgressBar $progressBar): array
    {
        $result = [];

        foreach ($files as $file) {
            /* @var $file SplFileInfo */
            try {
                $progressBar->setMessage(sprintf('Importing file %s', $file->getPathname()));
                $newFile = $this->addOrUpdateFile($file);
                $result[] = [
                    'error' => false,
                    'path' => $file->getPathname(),
                    'file' => $newFile->getId(),
                ];
            } catch (Throwable $exception) {
                $result[] = [
                    'error' => true,
                    'path' => $file->getPathname(),
                    'message' => $exception->getMessage(),
                ];
                if ($stopOnError) {
                    break;
                }
            }

            /* @noinspection DisconnectedForeachInstructionInspection */
            $progressBar->advance();
        }

        return $result;
    }

    private function addOrUpdateFile(SplFileInfo $file): File
    {
        $fileResource = fopen($file->getPathname(), 'rb+');
        $filePath = $this->mediaService->saveMedia($fileResource, MediaServiceInterface::JINYA_CONTENT);

        $name = $file->getBasename(sprintf('.%s', $file->getExtension()));

        if (null === ($newFile = $this->fileService->getByName($name))) {
            $newFile = new File();
        }
        $newFile->setName($name);
        $newFile->setPath($filePath);
        $this->fileService->saveOrUpdate($newFile);

        return $newFile;
    }

    private function formatFileOutput(array $importResult, OutputInterface $output): void
    {
        $table = new Table($output);
        $table->setHeaders(['Filename', 'Result']);

        foreach ($importResult as $item) {
            if ($item['error']) {
                $result = $item['message'];
            } else {
                $result = sprintf('File %s', $item['path']);
            }

            $table->addRow([$item['path'], $result]);
        }

        $table->render();
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('jinya:import:files')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory where the files are located')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Go recursive through all directories')
            ->addOption('stop-on-error', 'p', InputOption::VALUE_NONE);
    }
}
