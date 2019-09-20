<?php

/** @noinspection DisconnectedForeachInstructionInspection */

namespace Jinya\Command\Migration;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Artwork\Artwork;
use Jinya\Entity\Artwork\ArtworkPosition;
use Jinya\Entity\Gallery\ArtGallery;
use Jinya\Entity\Media\File;
use Jinya\Entity\Media\Gallery;
use Jinya\Entity\Media\GalleryFilePosition;
use Jinya\Entity\Media\Tag;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Jinya\Services\Galleries\ArtGalleryServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mime\MimeTypes;
use Throwable;

class ArtworksToFilesCommand extends Command
{
    /** @var ArtworkServiceInterface */
    private $artworkService;

    /** @var ArtGalleryServiceInterface */
    private $artGalleryService;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MimeTypes */
    private $mimeTypes;

    /**
     * ArtworksToFiles constructor.
     * @param ArtworkServiceInterface $artworkService
     * @param ArtGalleryServiceInterface $artGalleryService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ArtworkServiceInterface $artworkService,
        ArtGalleryServiceInterface $artGalleryService,
        MimeTypes $mimeTypes,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct();
        $this->artworkService = $artworkService;
        $this->artGalleryService = $artGalleryService;
        $this->entityManager = $entityManager;
        $this->mimeTypes = $mimeTypes;
    }

    protected function configure()
    {
        $this
            ->setName('jinya:migration:artworks')
            ->setDescription(<<<'DESCRIPTION'
Converts all artworks to the media manager structure created in 11.0.0\n
Also migrates the art_galleries, to the new gallery created in 11.0.0
DESCRIPTION
            )
            ->addOption(
                'file-prefix',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specifies the prefix the converted files should have',
                ''
            )
            ->addOption(
                'tags',
                null,
                InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
                'Specifies the tags the converted files should have',
                ['artwork']
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $artworks = $this->artworkService->getAll(0, PHP_INT_MAX);
        $tags = $input->getOption('tags');
        $prefix = $input->getOption('file-prefix');
        $artworkFileMapping = [];

        $failedArtworks = [];
        $migratedArtworks = [];

        $this->entityManager->beginTransaction();

        try {
            $artworkProgressBar = new ProgressBar($output, count($artworks));
            $artworkProgressBar->setMessage(sprintf('Migrating %d artworks...', count($artworks)));
            foreach ($artworkProgressBar->iterate($artworks) as $artwork) {
                /** @var Artwork $artwork */
                if (file_exists('public' . $artwork->getPicture()) && !is_dir('public' . $artwork->getPicture())) {
                    $file = new File();
                    $file->setName($prefix . $artwork->getName());
                    $file->setTags(new ArrayCollection(array_map(function (string $tag) {
                        $newTag = $this->entityManager->getRepository(Tag::class)->findOneBy(['tag' => $tag]);
                        if (null === $newTag) {
                            $newTag = new Tag();
                            $newTag->setTag($tag);
                        }

                        return $newTag;
                    }, $tags)));
                    $file->setPath($artwork->getPicture());
                    $mimeType = $this->mimeTypes->guessMimeType('public' . $artwork->getPicture());
                    $file->setType($mimeType ?? 'application/octet-stream');
                    $file->setCreator($artwork->getCreator());
                    $file->setCreatedAt($artwork->getCreatedAt());
                    $file->setUpdatedBy($artwork->getUpdatedBy());
                    $file->setLastUpdatedAt($artwork->getLastUpdatedAt());
                    $file->setHistory([]);

                    $this->entityManager->persist($file);
                    $this->entityManager->flush();
                    $artworkFileMapping[$artwork->getId()] = ['artwork' => $artwork, 'file' => $file];
                    $migratedArtworks[] = $artwork;
                } else {
                    $failedArtworks[] = $artwork;
                }
            }

            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
        }

        $resultTable = new Table($output);
        $resultTable->setHeaders(['Status', 'Artwork', 'Path']);
        foreach ($migratedArtworks as $migratedArtwork) {
            $resultTable->addRow(['Success', $migratedArtwork->getName(), $migratedArtwork->getPicture()]);
        }
        $resultTable->addRow(new TableSeparator());
        foreach ($failedArtworks as $failedArtwork) {
            $resultTable->addRow(['Failed', $failedArtwork->getName(), $failedArtwork->getPicture()]);
        }

        $resultTable->render();
        $output->writeln('');
        $artGalleries = $this->artGalleryService->getAll(0, PHP_INT_MAX);
        $galleryProgressBar = new ProgressBar($output, count($artGalleries));
        $galleryProgressBar->setMessage(sprintf('Migrating %d galleries...', count($artGalleries)));

        $this->entityManager->beginTransaction();

        try {
            foreach ($galleryProgressBar->iterate($artGalleries) as $artGallery) {
                /** @var ArtGallery $artGallery */
                $gallery = new Gallery();
                $gallery->setType($artGallery->isMasonry() ? 'masonry' : 'sequence');
                $gallery->setOrientation($artGallery->getOrientation());
                $gallery->setName($prefix . $artGallery->getName());
                $gallery->setDescription($artGallery->getDescription() ?? '');
                $gallery->setSlug($artGallery->getSlug());
                $gallery->setLastUpdatedAt($artGallery->getLastUpdatedAt());
                $gallery->setUpdatedBy($artGallery->getUpdatedBy());
                $gallery->setCreatedAt($artGallery->getCreatedAt());
                $gallery->setCreator($artGallery->getCreator());
                $gallery->setHistory([]);

                $this->entityManager->persist($gallery);
                $this->entityManager->flush();

                foreach ($artGallery->getArtworks() as $artworkPosition) {
                    /** @var ArtworkPosition $artworkPosition */
                    $artwork = $artworkPosition->getArtwork();
                    if (array_key_exists($artwork->getId(), $artworkFileMapping)) {
                        $galleryFilePosition = new GalleryFilePosition();
                        $galleryFilePosition->setPosition($artworkPosition->getPosition());
                        $galleryFilePosition->setGallery($gallery);
                        $galleryFilePosition->setFile($artworkFileMapping[$artwork->getId()]['file']);
                        $this->entityManager->persist($galleryFilePosition);
                        $this->entityManager->flush();
                    }
                }
            }

            $this->entityManager->commit();
        } catch (Throwable $exception) {
            $this->entityManager->rollback();
            $output->writeln(sprintf('<error>%s</error>', $exception->getMessage()));
        }
        $output->writeln('');
    }
}
