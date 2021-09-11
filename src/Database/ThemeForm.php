<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;

class ThemeForm extends ThemeHelperEntity implements FormattableEntityInterface
{

    public int $formId = -1;

    /**
     * Finds a form by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeForm|null
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByThemeAndName(int $themeId, string $name): ?ThemeForm
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_form', new self());
    }

    /**
     * Finds the forms for the given theme
     *
     * @param int $themeId
     * @return Iterator
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_form', new self());
    }

    /**
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\UniqueFailedException
     * @throws Exceptions\InvalidQueryException
     */
    #[ArrayShape(['name' => "string", 'form' => "array"])] public function format(): array
    {
        return [
            'name' => $this->name,
            'form' => $this->getForm()->format(),
        ];
    }

    /**
     * Gets the form of the theme form
     *
     * @return Form|null
     *
     * @throws Exceptions\ForeignKeyFailedException
     * @throws Exceptions\InvalidQueryException
     * @throws Exceptions\UniqueFailedException
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function create(): void
    {
        $this->internalCreate('theme_form');
    }

    /**
     * @inheritDoc
     */
    public function delete(): void
    {
        $this->internalDelete('theme_form');
    }

    /**
     * @inheritDoc
     * @throws Exceptions\UniqueFailedException
     */
    public function update(): void
    {
        $this->internalUpdate('theme_form');
    }
}