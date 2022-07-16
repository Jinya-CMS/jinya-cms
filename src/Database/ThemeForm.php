<?php

namespace App\Database;

use App\Database\Utils\ThemeHelperEntity;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;

/**
 * This class contains a form connected to a theme
 */
class ThemeForm extends ThemeHelperEntity
{
    /** @var int The form ID */
    public int $formId = -1;

    /**
     * Finds a form by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeForm|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     * @throws NoResultException
     * @throws NoResultException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeForm
    {
        /**
         * @phpstan-ignore-next-line
         */
        return self::fetchByThemeAndName($themeId, $name, 'theme_form', new self());
    }

    /**
     * Finds the forms for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_form', new self());
    }

    /**
     * Formats the theme form into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    #[ArrayShape(['name' => 'string', 'form' => 'array'])] public function format(): array
    {
        return [
            'name' => $this->name,
            'form' => $this->getForm()?->format(),
        ];
    }

    /**
     * Gets the form of the theme form
     *
     * @return Form|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    /**
     * Creates the current theme form
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function create(): void
    {
        $this->internalCreate('theme_form');
    }

    /**
     * Deletes the current theme form
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function delete(): void
    {
        $this->internalDelete('theme_form');
    }

    /**
     * Updates the current theme form
     *
     * @return void
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws InvalidQueryException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_form');
    }
}
