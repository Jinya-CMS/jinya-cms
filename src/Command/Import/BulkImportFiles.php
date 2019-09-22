<?php

/** @noinspection HtmlUnknownTag */

namespace Jinya\Command\Import;

use Iterator;
use Jinya\Entity\Media\File;
use Jinya\Framework\Security\AuthenticatedCommand;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\GalleryFilePositionServiceInterface;
use Jinya\Services\Media\GalleryServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Slug\SlugServiceInterface;
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
    /** @var GalleryServiceInterface */
    private $galleryService;

    /** @var FileServiceInterface */
    private $fileService;

    /** @var MediaServiceInterface */
    private $mediaService;

    /** @var SlugServiceInterface */
    private $slugService;

    /** @var GalleryFilePositionServiceInterface */
    private $galleryFilePositionService;

    /**
     * BulkImportFiles constructor.
     * @param GalleryServiceInterface $galleryService
     * @param FileServiceInterface $fileService
     * @param MediaServiceInterface $mediaService
     * @param SlugServiceInterface $slugService
     * @param GalleryFilePositionServiceInterface $galleryFilePositionService
     * @param UserServiceInterface $userService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        GalleryServiceInterface $galleryService,
        FileServiceInterface $fileService,
        MediaServiceInterface $mediaService,
        SlugServiceInterface $slugService,
        GalleryFilePositionServiceInterface $galleryFilePositionService,
        UserServiceInterface $userService,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($tokenStorage, $userService);
        $this->galleryService = $galleryService;
        $this->fileService = $fileService;
        $this->mediaService = $mediaService;
        $this->slugService = $slugService;
        $this->galleryFilePositionService = $galleryFilePositionService;
    }

    protected function authenticatedExecute(InputInterface $input, OutputInterface $output): void
    {
        $directory = $input->getArgument('directory');
        $recursive = $input->getOption('recursive');
        $createGallery = $input->getOption('create-gallery');
        $stopOnError = $input->getOption('stop-on-error');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% -- %message%');

        [$files, $count] = $this->scanDirectory($directory, $recursive);
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('custom');
        $progressBar->setMessage('Start file import');
        $progressBar->start();
        [$importResult, $error] = $this->importFiles($files, $stopOnError, $progressBar);
        $progressBar->finish();
        $this->formatFileOutput($importResult, $output);

        if (!($error && $stopOnError) && $createGallery) {
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat('custom');
            $progressBar->setMessage('Start import into galleries');
            $progressBar->start();
        } else {
            $output->writeln('<error>Stopped import because of an error</error>');
        }
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
        $error = false;

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
                    $error = true;

                    break;
                }
            }

            /* @noinspection DisconnectedForeachInstructionInspection */
            $progressBar->advance();
        }

        return [$result, $error];
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
                $result = sprintf('File %s', $item['file']);
            }

            $table->addRow([$item['file'], $result]);
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
            ->addOption('stop-on-error', 'p', InputOption::VALUE_NONE)
            ->addOption('create-gallery', 'c', InputOption::VALUE_NONE, 'Create galleries based on directories');
    }
}
