<?php

namespace Jinya\Cms\Console;

use Jinya\Cms\Authentication\CurrentUser;
use Jinya\Cms\Database\Artist;
use Jinya\Cms\Database\BlogPostSection;
use Jinya\Cms\Database\ClassicPage;
use Jinya\Cms\Database\File;
use Jinya\Cms\Database\ModernPageSection;
use Jinya\Cms\Storage\StorageBaseService;

/**
 * @codeCoverageIgnore I really don't know how to unit test this, but it has undergone rigid by hand testing
 */
#[JinyaCommand('file-migrate')]
class MigrateFilesCommand extends AbstractCommand
{
    /**
     * @inheritDoc
     */
    public function run(): void
    {
        $this->climate->arguments->add([
            'userId' => [
                'longPrefix' => 'userId',
                'shortPrefix' => 'u',
                'required' => true,
            ],
            'force' => [
                'longPrefix' => 'force',
                'shortPrefix' => 'f',
                'required' => false,
                'defaultValue' => false,
                'noValue' => true,
            ],
        ]);
        $this->climate->arguments->parse();
        $this->climate->info(
            'The migration command will move all files in the database that are not in public/jinya-content to public/jinya-content and renames them'
        );

        if ($this->climate->arguments->get('force') === true) {
            $migrate = $this->climate->confirm(
                'Do you want to start the migration?'
            );
            if (!$migrate->confirmed()) {
                $this->climate->info('Migration canceled');
                return;
            }
        }

        $user = Artist::findById((int)$this->climate->arguments->get('userId'));
        if (!$user) {
            $this->climate->error('The user was not found');
            return;
        }

        CurrentUser::$currentUser = $user;

        $this->climate->info('Load all files from the database that are not in jinya-content');
        /** @var File[] $files */
        $files = iterator_to_array(File::findByFilters(["path NOT LIKE '\\/jinya-content%'" => []]));
        $renamedFiles = [];

        $this->climate->info('Start migration of ' . count($files) . ' files');
        $progress = $this->climate->progress(count($files));
        $trimmedPublicPath = rtrim(StorageBaseService::PUBLIC_PATH, '/');
        foreach ($files as $idx => $file) {
            $progress->current($idx, 'Handle file ' . $file->name);
            $oldShortPath = $file->path;
            $oldFullPath = $trimmedPublicPath . $oldShortPath;
            if (file_exists($oldFullPath)) {
                $progress->current($idx, 'Hash file ' . $file->name);
                $fileName = hash_file('sha256', $oldFullPath);
                $newShortPath = '/jinya-content/' . $fileName;
                $newFullPath = $trimmedPublicPath . $newShortPath;

                $progress->current($idx, 'Copy file ' . $file->name);
                copy($oldFullPath, $newFullPath);
                $file->path = $newShortPath;

                $progress->current($idx, 'Save file ' . $file->name);
                $file->update();

                $renamedFiles[$oldShortPath] = "/image.php?id=$file->id&type=webp";
            }

            $progress->advance();
        }

        $this->climate->info('Replace all references in classic pages');
        $pages = iterator_to_array(ClassicPage::findAll());
        $progress = $this->climate->progress(count($pages));
        foreach ($pages as $page) {
            $page->content = str_replace(array_keys($renamedFiles), array_values($renamedFiles), $page->content);
            $page->update();
            $progress->advance();
        }

        $this->climate->info('Replace all references in modern page sections');
        $query = ModernPageSection::getQueryBuilder()
            ->newSelect()
            ->from(ModernPageSection::getTableName())
            ->cols([
                'id',
                'html',
            ])
            ->where("html IS NOT NULL AND html <> ''");

        /** @var array{id: int, html: string}[] $data */
        $data = ModernPageSection::executeQuery($query);
        $progress = $this->climate->progress(count($data));
        foreach ($data as $item) {
            $html = str_replace(array_keys($renamedFiles), array_values($renamedFiles), $item['html']);
            $query = ModernPageSection::getQueryBuilder()
                ->newUpdate()
                ->table(ModernPageSection::getTableName())
                ->set('html', ':html')
                ->where('id = :id', ['id' => $item['id']])
                ->bindValue('html', $html);
            ModernPageSection::executeQuery($query);

            $progress->advance();
        }
        $this->climate->info('Updated ' . count($data) . ' modern page sections');

        $this->climate->info('Replace all references in blog page sections');
        $query = BlogPostSection::getQueryBuilder()
            ->newSelect()
            ->from(BlogPostSection::getTableName())
            ->cols([
                'id',
                'html',
            ])
            ->where("html IS NOT NULL AND html <> ''");

        /** @var array{id: int, html: string}[] $data */
        $data = BlogPostSection::executeQuery($query);
        $progress = $this->climate->progress(count($data));
        foreach ($data as $item) {
            $html = str_replace(array_keys($renamedFiles), array_values($renamedFiles), $item['html']);
            $query = BlogPostSection::getQueryBuilder()
                ->newUpdate()
                ->table(BlogPostSection::getTableName())
                ->set('html', ':html')
                ->where('id = :id', ['id' => $item['id']])
                ->bindValue('html', $html);
            BlogPostSection::executeQuery($query);

            $progress->advance();
        }
        $this->climate->info('Updated ' . count($data) . ' blog post sections');

        $this->climate->info('Load all artists from the database whose profile picture is not in jinya-content');
        /** @var Artist[] $users */
        $users = iterator_to_array(Artist::findByFilters(["profile_picture NOT LIKE '\\/jinya-content%'" => []]));

        $this->climate->info('Start migration of ' . count($users) . ' artists');
        $progress = $this->climate->progress(count($users));
        $trimmedPublicPath = rtrim(StorageBaseService::PUBLIC_PATH, '/');
        foreach ($users as $idx => $artist) {
            $progress->current($idx, 'Handle file ' . $artist->artistName);
            $oldShortPath = $artist->profilePicture;
            $oldFullPath = $trimmedPublicPath . $oldShortPath;
            if (file_exists($oldFullPath)) {
                $progress->current($idx, 'Hash profile picture of ' . $artist->artistName);
                $fileName = hash_file('sha256', $oldFullPath);
                $newShortPath = '/jinya-content/' . $fileName;
                $newFullPath = $trimmedPublicPath . $newShortPath;

                $progress->current($idx, 'Copy profile picture of ' . $artist->artistName);
                copy($oldFullPath, $newFullPath);
                $artist->profilePicture = $newShortPath;

                $progress->current($idx, 'Save artist ' . $artist->artistName);
                $artist->update();
            }

            $progress->advance();
        }

        $this->climate->info('Recreate the cache');
        $cacheCommand = new FileCacheCommand();
        $cacheCommand->run();
    }
}
