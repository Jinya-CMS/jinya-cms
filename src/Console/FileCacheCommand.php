<?php

namespace App\Console;

use App\Database\File;
use App\Storage\StorageBaseService;
use App\Theming\Extensions\FileExtension;
use Intervention\Image\ImageManager;
use Throwable;

/**
 *
 */
#[JinyaCommand('file-cache')]
class FileCacheCommand extends AbstractCommand
{

    public function run(): void
    {
        $forceRecache = $this->climate->arguments->get('--force-rebuild') ?: false;
        $this->climate->info('Load all files from database');
        $files = File::findAll();
        $manager = new ImageManager(['driver' => 'imagick']);
        foreach ($files as $file) {
            try {
                $this->climate->info("Process file $file->name");
                $image = $manager->make(StorageBaseService::BASE_PATH . '/public/' . $file->path);
                foreach (FileExtension::RESOLUTIONS_FOR_SOURCE as $width) {
                    $basepath = StorageBaseService::BASE_PATH . '/public/' . $file->path . '-' . $width . 'w.';
                    if (!$forceRecache && file_exists($basepath . 'webp')) {
                        $this->climate->info("File cached for WebP in resolution $width");
                    } else {
                        $this->climate->info("Create file cache for WebP and resolution $width");
                        $image->save($basepath . 'webp');
                        $this->climate->info("File cached for WebP in resolution $width");
                    }
                    if (!$forceRecache && file_exists($basepath . 'png')) {
                        $this->climate->info("File cached for PNG in resolution $width");
                    } else {
                        $this->climate->info("Create file cache for PNG and resolution $width");
                        $image->save($basepath . 'png');
                        $this->climate->info("File cached for PNG in resolution $width");
                    }
                    if (!$forceRecache && file_exists($basepath . 'jpg')) {
                        $this->climate->info("File cached for JPEG in resolution $width");
                    } else {
                        $this->climate->info("Create file cache for JPEG and resolution $width");
                        $image->save($basepath . 'jpg');
                        $this->climate->info("File cached for JPEG in resolution $width");
                    }
                }
            } catch (Throwable $e) {
                $this->climate->error("Failed to process file $file->name");
                $this->climate->error($e->getMessage());
                $this->climate->error($e->getTraceAsString());
            }
        }
    }
}