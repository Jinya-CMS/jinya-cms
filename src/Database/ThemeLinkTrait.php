<?php

namespace Jinya\Cms\Database;

use Iterator;
use Jinya\Cms\Logging\Logger;
use Jinya\Database\EntityTrait;
use Jinya\Database\Exception\ForeignKeyFailedException;
use Jinya\Database\Exception\UniqueFailedException;
use PDOException;
use Psr\Log\LoggerInterface;

/**
 * @method static string getLinkIdProperty()
 * @method static string getLinkIdColumn()
 * @property int $themeId
 * @property string $name
 */
trait ThemeLinkTrait
{
    use EntityTrait;

    private readonly LoggerInterface $logger;

    public function __construct()
    {
        $this->logger = Logger::getLogger();
    }

    /**
     * Finds a blog category by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return self|null
     */
    public static function findByThemeAndName(int $themeId, string $name): ?self
    {
        $class = self::class;
        $logger = Logger::getLogger();
        $logger->debug("Find theme $class by theme and name", ['themeId' => $themeId, 'name' => $name]);
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                self::getLinkIdColumn(),
            ])
            ->where('theme_id = :themeId AND name = :name', ['themeId' => $themeId, 'name' => $name]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        if (empty($data)) {
            return null;
        }

        return self::fromArray($data[0]);
    }

    /**
     * Finds all theme blog categories in the theme with the given ID
     *
     * @param int $themeId
     * @return Iterator<self>
     */
    public static function findByTheme(int $themeId): Iterator
    {
        $logger = Logger::getLogger();
        $class = self::class;
        $logger->debug("Find $class by theme", ['themeId' => $themeId]);
        $query = self::getQueryBuilder()
            ->newSelect()
            ->from(self::getTableName())
            ->cols([
                'theme_id',
                'name',
                self::getLinkIdColumn(),
            ])
            ->where('theme_id = :themeId', ['themeId' => $themeId]);

        /** @var array<string, mixed>[] $data */
        $data = self::executeQuery($query);
        foreach ($data as $item) {
            yield self::fromArray($item);
        }
    }

    /**
     * Creates the current theme blog category
     *
     * @return void
     */
    public function create(): void
    {
        $class = self::class;
        $this->logger->debug(
            "Create new theme $class",
            [
                self::getLinkIdProperty() => $this->{self::getLinkIdProperty()},
                'themeId' => $this->themeId
            ]
        );
        $query = self::getQueryBuilder()
            ->newInsert()
            ->into(self::getTableName())
            ->addRow([
                'theme_id' => $this->themeId,
                'name' => $this->name,
                self::getLinkIdColumn() => $this->{self::getLinkIdProperty()},
            ]);

        try {
            self::executeQuery($query);
        } catch (PDOException $exception) {
            $this->logger->error("Failed to create $class", [
                self::getLinkIdProperty() => $this->{self::getLinkIdProperty()},
                'themeId' => $this->themeId,
                'exception' => $exception
            ]);
            $errorInfo = $exception->errorInfo ?? ['', ''];
            if ($errorInfo[1] === 1062) {
                throw new UniqueFailedException($exception, self::getPDO());
            }

            if ($errorInfo[1] === 1452) {
                throw new ForeignKeyFailedException($exception, self::getPDO());
            }

            throw $exception;
        }
    }

    /**
     * @inheritDoc
     */
    public function update(): void
    {
        $query = self::getQueryBuilder()
            ->newUpdate()
            ->table(self::getTableName())
            /** @phpstan-ignore argument.type */
            ->set(self::getLinkIdColumn(), $this->{self::getLinkIdProperty()})
            ->where('theme_id = :themeId and name = :name', ['themeId' => $this->themeId, 'name' => $this->name]);

        try {
            self::executeQuery($query);
        } catch (PDOException $exception) {
            $errorInfo = $exception->errorInfo ?? ['', ''];
            if ($errorInfo[1] === 1062) {
                throw new UniqueFailedException($exception, self::getPDO());
            }

            if ($errorInfo[1] === 1452) {
                throw new ForeignKeyFailedException($exception, self::getPDO());
            }

            throw $exception;
        }
    }
}
