<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 09.11.2017
 * Time: 19:32
 */

namespace Jinya\Services\Media;

use Jinya\Framework\Events\Media\MediaDeleteEvent;
use Jinya\Framework\Events\Media\MediaGetEvent;
use Jinya\Framework\Events\Media\MediaMoveEvent;
use Jinya\Framework\Events\Media\MediaSaveEvent;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MediaService implements MediaServiceInterface
{
    /** @var string */
    private $kernelProjectDir;

    /** @var string */
    private $tmpDir;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * MediaService constructor.
     * @param string $kernelProjectDir
     * @param string $tmpDir
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(string $kernelProjectDir, string $tmpDir, EventDispatcherInterface $eventDispatcher)
    {
        $this->kernelProjectDir = $kernelProjectDir;
        $this->tmpDir = $tmpDir;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Saves the media to the storage and return the http url
     *
     * @param resource|UploadedFile $file
     * @param string $type
     * @return string
     */
    public function saveMedia($file, string $type): string
    {
        $preSave = $this->eventDispatcher->dispatch(new MediaSaveEvent($file, $type), MediaSaveEvent::PRE_SAVE);
        if (empty($preSave->getLocation())) {
            $tmpFilename = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid('media-temp', true);
            file_put_contents($tmpFilename, $file);

            $location = $this->moveFile($tmpFilename, $type);
        } else {
            $location = $preSave->getLocation();
        }

        $event = new MediaSaveEvent($file, $type);
        $event->setLocation($location);

        $this->eventDispatcher->dispatch($event, MediaSaveEvent::POST_SAVE);

        return $location;
    }

    /**
     * @param string $type
     * @param string $oldFile
     * @return string
     */
    private function moveFile(string $oldFile, string $type): string
    {
        $directory = $this->getFilePath($type);
        if (!@mkdir($directory, 0775, true) && !@is_dir($directory)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $directory));
        }

        $hashCtx = hash_init('sha256');
        hash_update_file($hashCtx, $oldFile);
        $hash = hash_final($hashCtx);

        $filename = "/$directory/$hash";

        $fs = new Filesystem();
        $fs->rename($oldFile, $filename, true);

        return "/public/$type/$hash";
    }

    private function getFilePath(string $type): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            $this->kernelProjectDir,
            'public',
            'public',
            $type,
        ]);
    }

    /**
     * Deletes the media saved under the given url
     *
     * @param string $url
     */
    public function deleteMedia(string $url): void
    {
        $parts = explode("\\/", $url);
        $parts = array_reverse($parts);
        [$filename, $type] = $parts;

        $pre = $this->eventDispatcher->dispatch(new MediaDeleteEvent($type, $filename), MediaDeleteEvent::PRE_DELETE);
        if (!$pre->isCancel()) {
            unlink($this->getFilePath($type) . $filename);
            $this->eventDispatcher->dispatch(new MediaDeleteEvent($type, $filename), MediaDeleteEvent::POST_DELETE);
        }
    }

    /**
     * Gets the media as SplFileInfo
     *
     * @param string $path
     * @return SplFileInfo
     */
    public function getMedia(string $path): SplFileInfo
    {
        $pre = $this->eventDispatcher->dispatch(new MediaGetEvent($path), MediaGetEvent::PRE_GET);
        if (empty($pre->getResult())) {
            $file = new SplFileInfo($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . $path);
        } else {
            $file = $pre->getResult();
        }

        $event = new MediaGetEvent($path);
        $event->setResult($file);
        $this->eventDispatcher->dispatch($event, MediaGetEvent::POST_GET);

        return $file;
    }

    /**
     * Moves a file from the given path to the correct media path
     *
     * @param string $from
     * @param string $type
     * @return string
     */
    public function moveMedia(string $from, string $type): string
    {
        $pre = $this->eventDispatcher->dispatch(new MediaMoveEvent($from, $type), MediaMoveEvent::PRE_MOVE);
        if (empty($pre->getLocation())) {
            $location = $this->moveFile($from, $type);
        } else {
            $location = $pre->getLocation();
        }

        $event = new MediaMoveEvent($from, $type);
        $event->setLocation($location);
        $this->eventDispatcher->dispatch($event, MediaMoveEvent::POST_MOVE);

        return $location;
    }
}
