<?php

namespace Jinya\Cms\Database;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Updatable;

/**
 * This class contains a file connected to a theme
 */
#[Table('theme_file')]
class ThemeFile implements Creatable, Updatable, Deletable
{
    use EntityTrait;
    use DeletableEntityTrait;
    use ThemeLinkTrait;

    /** @var string The theme name */
    #[Column]
    public string $name = '';

    /** @var int The theme id */
    #[Column(sqlName: 'theme_id')]
    public int $themeId = -1;

    /** @var int The file ID */
    #[Column(sqlName: 'file_id')]
    public int $fileId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'fileId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'file_id';
    }

    /**
     * Formats the theme file into an array
     *
     * @return array{'name': string, 'file': array<string, mixed>|null}
     * @throws Exception
     */
    #[ArrayShape(['name' => 'string', 'file' => 'array'])]
    public function format(): array
    {
        return [
            'name' => $this->name,
            'file' => $this->getFile()?->format(),
        ];
    }

    /**
     * Gets the file of the theme file
     *
     * @return File|null
     *
     */
    public function getFile(): ?File
    {
        return File::findById($this->fileId);
    }
}
