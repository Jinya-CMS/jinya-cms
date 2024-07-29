<?php

namespace Jinya\Cms\Console;

use Iterator;
use Jinya\Cms\Database\File;
use Jinya\Cms\Storage\ConversionService;
use Throwable;

/**
 * This command generates the file cache of uploaded images
 */
#[JinyaCommand('file-cache')]
class FileCacheCommand extends AbstractCommand
{
    /**
     * Recreates the file cache
     *
     * @return void
     */
    public function run(): void
    {
        $this->climate->info('Load all files from database');
        /** @var Iterator<File> $files */
        $files = File::findAll();
        $conversionService = new ConversionService();
        foreach ($files as $file) {
            try {
                $this->climate->info("Process file $file->name");
                $conversionService->convertFile($file->id);
            } catch (Throwable $e) {
                $this->climate->error("Failed to process file $file->name");
                $this->climate->error($e->getMessage());
                $this->climate->error($e->getTraceAsString());
            }
        }
    }
}
