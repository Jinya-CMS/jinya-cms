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
use SplFileInfo;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
        $preSave = $this->eventDispatcher->dispatch(MediaSaveEvent::PRE_SAVE, new MediaSaveEvent($file, $type));
        if (empty($preSave->getLocation())) {
            $tmpFilename = $this->tmpDir . DIRECTORY_SEPARATOR . uniqid();
            file_put_contents($tmpFilename, $file);

            $location = $this->moveFile($tmpFilename, $type);
        } else {
            $location = $preSave->getLocation();
        }

        $event = new MediaSaveEvent($file, $type);
        $event->setLocation($location);

        $this->eventDispatcher->dispatch(MediaSaveEvent::POST_SAVE, $event);

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
        @mkdir($directory, 0775, true);

        $hashCtx = hash_init('sha256');
        hash_update_file($hashCtx, $oldFile);
        $hash = hash_final($hashCtx);

        $filename = $directory . $hash;

        $fs = new Filesystem();
        $fs->rename($oldFile, $filename, true);

        return "/public/$type/${hash}";
    }

    private function getFilePath(string $type): string
    {
        return $this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR;
    }

    /**
     * Deletes the media saved under the given url
     *
     * @param string $url
     */
    public function deleteMedia(string $url)
    {
        $parts = preg_split('/\\//', $url);
        $parts = array_reverse($parts);
        $filename = $parts[0];
        $type = $parts[1];

        $pre = $this->eventDispatcher->dispatch(MediaDeleteEvent::PRE_DELETE, new MediaDeleteEvent($type, $filename));
        if (!$pre->isCancel()) {
            unlink($this->getFilePath($type) . $filename);
            $this->eventDispatcher->dispatch(MediaDeleteEvent::POST_DELETE, new MediaDeleteEvent($type, $filename));
        }
    }

    /**
     * Gets the media as SplFileInfo
     *
     * @param string $path
     * @return SplFileInfo|string
     */
    public function getMedia(string $path)
    {
        $pre = $this->eventDispatcher->dispatch(MediaGetEvent::PRE_GET, new MediaGetEvent($path));
        if (empty($pre->getResult())) {
            $file = new SplFileInfo($this->kernelProjectDir . DIRECTORY_SEPARATOR . 'public' . $path);
        } else {
            $file = $pre->getResult();
        }

        $event = new MediaGetEvent($path);
        $event->setResult($file);
        $this->eventDispatcher->dispatch(MediaGetEvent::POST_GET, $event);

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
        $pre = $this->eventDispatcher->dispatch(MediaMoveEvent::PRE_MOVE, new MediaMoveEvent($from, $type));
        if (empty($pre->getLocation())) {
            $location = $this->moveFile($from, $type);
        } else {
            $location = $pre->getLocation();
        }

        $event = new MediaMoveEvent($from, $type);
        $event->setLocation($location);
        $this->eventDispatcher->dispatch(MediaMoveEvent::POST_MOVE, $event);

        return $location;
    }
}
