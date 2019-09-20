<?php

/** @noinspection HtmlUnknownTag */

namespace Jinya\Command\Import;

use InvalidArgumentException;
use Iterator;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Framework\Security\AuthenticatedCommand;
use Jinya\Services\Artworks\ArtworkPositionServiceInterface;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Jinya\Services\Media\ConversionServiceInterface;
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

class BulkImportArtworks extends AuthenticatedCommand
{
    /** @var ArtGalleryServiceInterface */
    private $galleryService;

    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var MediaServiceInterface */
    private $mediaService;

    /** @var ConversionServiceInterface */
    private $conversionService;

    /** @var SlugServiceInterface */
    private $slugService;

    /** @var ArtworkPositionServiceInterface */
    private $artworkPositionService;

    /**
     * BulkImportArtworks constructor.
     * @param ArtGalleryServiceInterface $galleryService
     * @param ArtworkServiceInterface $artworkService
     * @param MediaServiceInterface $mediaService
     * @param ConversionServiceInterface $conversionService
     * @param SlugServiceInterface $slugService
     * @param ArtworkPositionServiceInterface $artworkPositionService
     * @param UserServiceInterface $userService
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        ArtGalleryServiceInterface $galleryService,
        ArtworkServiceInterface $artworkService,
        MediaServiceInterface $mediaService,
        ConversionServiceInterface $conversionService,
        SlugServiceInterface $slugService,
        ArtworkPositionServiceInterface $artworkPositionService,
        UserServiceInterface $userService,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($tokenStorage, $userService);
        $this->galleryService = $galleryService;
        $this->artworkService = $artworkService;
        $this->mediaService = $mediaService;
        $this->conversionService = $conversionService;
        $this->slugService = $slugService;
        $this->artworkPositionService = $artworkPositionService;
    }

    protected function authenticatedExecute(InputInterface $input, OutputInterface $output): void
    {
        $directory = $input->getArgument('directory');
        $recursive = $input->getOption('recursive');
        $createGallery = $input->getOption('create-gallery');
        $stopOnError = $input->getOption('stop-on-error');
        $convert = $input->getOption('convert');
        $targetType = $input->getOption('conversion-target');

        ProgressBar::setFormatDefinition('custom', ' %current%/%max% [%bar%] %percent:3s%% -- %message%');

        [$files, $count] = $this->scanDirectory($directory, $recursive);
        $progressBar = new ProgressBar($output, $count);
        $progressBar->setFormat('custom');
        $progressBar->setMessage('Start artwork import');
        $progressBar->start();
        [$importResult, $error] = $this->importFiles($files, $convert, $targetType, $stopOnError, $progressBar);
        $progressBar->finish();
        $this->formatArtworkOutput($importResult, $output);

        if (!($error && $stopOnError) && $createGallery) {
            $progressBar = new ProgressBar($output, $count);
            $progressBar->setFormat('custom');
            $progressBar->setMessage('Start import into galleries');
            $progressBar->start();
            [$importResult] = $this->importArtworksToGalleries($files, $stopOnError, $progressBar);
            $this->formatGalleryOutput($importResult, $output);
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

    private function importFiles(
        Iterator $files,
        bool $convert,
        string $targetType,
        bool $stopOnError,
        ProgressBar $progressBar
    ): array {
        $result = [];
        $error = false;

        foreach ($files as $file) {
            /* @var $file SplFileInfo */
            try {
                $progressBar->setMessage(sprintf('Importing file %s', $file->getPathname()));
                [$artwork, $action] = $this->addOrUpdateFile($file, $convert, $targetType);
                $result[] = [
                    'error' => false,
                    'file' => $file->getPathname(),
                    'artwork' => $artwork->getSlug(),
                    'action' => $action,
                ];
            } catch (Throwable $exception) {
                $result[] = [
                    'error' => true,
                    'file' => $file->getPathname(),
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

    private function addOrUpdateFile(SplFileInfo $file, bool $convert, string $targetType): array
    {
        if ($convert) {
            switch ($targetType) {
                case 'png':
                    $type = IMAGETYPE_PNG;

                    break;
                case 'gif':
                    $type = IMAGETYPE_GIF;

                    break;
                case 'webp':
                    $type = IMAGETYPE_WEBP;

                    break;
                case 'jpg':
                    $type = IMAGETYPE_JPEG;

                    break;
                default:
                    throw new InvalidArgumentException('Target type must be one of png, jpg, webp or gif');

                    break;
            }
            $fileResource = $this->conversionService->convertImage($file->getContents(), $type);
        } else {
            $fileResource = fopen($file->getPathname(), 'rb+');
        }
        $filePath = $this->mediaService->saveMedia($fileResource, MediaServiceInterface::CONTENT_IMAGE);

        $name = $file->getBasename(sprintf('.%s', $file->getExtension()));
        $slug = $this->slugService->generateSlug($name);

        try {
            $artwork = $this->artworkService->get($slug);
            $action = 'updated';
        } catch (Throwable $exception) {
            $artwork = new Artwork();
            $action = 'created';
        }
        $artwork->setName($name);
        $artwork->setSlug($slug);
        $artwork->setPicture($filePath);
        $this->artworkService->saveOrUpdate($artwork);

        return [$artwork, $action];
    }

    private function formatArtworkOutput(array $importResult, OutputInterface $output): void
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Filename', 'Result']);

        foreach ($importResult as $item) {
            if ($item['error']) {
                $result = $item['message'];
            } else {
                $result = sprintf('Artwork %s %s', $item['artwork'], $item['action']);
            }
            $table->addRow([$item['file'], $result]);
        }

        $table->render();
    }

    private function importArtworksToGalleries(Iterator $files, bool $stopOnError, ProgressBar $progressBar): array
    {
        $position = 0;
        $result = [];
        $error = false;

        foreach ($files as $file) {
            /* @var $file SplFileInfo */
            try {
                $galleryName = basename($file->getPath());
                $progressBar->setMessage(sprintf('Import gallery %s', $galleryName));
                $gallerySlug = $this->slugService->generateSlug($galleryName);
                $this->createGalleryIfNotExists($galleryName);
                $artworkSlug = $this->slugService->generateSlug($file->getBasename($file->getExtension()));
                $this->artworkPositionService->savePosition($gallerySlug, $artworkSlug, $position);
                $result[] = [
                    'error' => false,
                    'file' => $file->getPathname(),
                    'gallery' => $gallerySlug,
                    'artwork' => $artworkSlug,
                ];

                ++$position;
                $progressBar->advance();
            } catch (Throwable $exception) {
                $result[] = [
                    'error' => true,
                    'file' => $file->getPathname(),
                    'message' => $exception->getMessage(),
                ];
                if ($stopOnError) {
                    $error = true;

                    break;
                }
            }
        }

        return [$result, $error];
    }

    private function createGalleryIfNotExists(string $galleryName): ArtGallery
    {
        $slug = $this->slugService->generateSlug($galleryName);

        try {
            $gallery = $this->galleryService->get($slug);
        } catch (Throwable $exception) {
            $gallery = new ArtGallery();
            $gallery->setName($galleryName);

            $this->galleryService->saveOrUpdate($gallery);
        }

        return $gallery;
    }

    private function formatGalleryOutput(array $importResult, OutputInterface $output): void
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Filename', 'Result']);

        foreach ($importResult as $item) {
            if ($item['error']) {
                $result = $item['message'];
            } else {
                $result = sprintf('Artwork %s imported into Gallery %s', $item['artwork'], $item['gallery']);
            }
            $table->addRow([$item['file'], $result]);
        }

        $table->render();
    }

    protected function configure(): void
    {
        parent::configure();
        $this
            ->setName('jinya:import:artworks')
            ->addArgument('directory', InputArgument::REQUIRED, 'The directory where the files are located')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, 'Go recursive through all directories')
            ->addOption('convert', 'o', InputOption::VALUE_NONE, 'Should convert the file before import')
            ->addOption(
                'conversion-target',
                't',
                InputOption::VALUE_OPTIONAL,
                'The type the file should be converted to, can be any of png, jpg, webp or gif',
                'png'
            )
            ->addOption('stop-on-error', 'p', InputOption::VALUE_NONE)
            ->addOption('create-gallery', 'c', InputOption::VALUE_NONE, 'Create galleries based on directories');
    }
}
