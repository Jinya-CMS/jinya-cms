<?php

namespace App\Database;

use App\Database\Utils\FormattableEntityInterface;
use App\Database\Utils\ThemeHelperEntity;
use Exception;
use Iterator;

class ThemeForm extends ThemeHelperEntity implements FormattableEntityInterface
{

    public string $name;
    public int $formId;
    public int $themeId;

    /**
     * Finds a form by name and theme
     *
     * @param int $themeId
     * @param string $name
     * @return ThemeForm
     */
    public static function findByThemeAndName(int $themeId, string $name): ThemeForm
    {
        return self::fetchByThemeAndName($themeId, $name, 'theme_form', new self());
    }

    /**
     * Finds the forms for the given theme
     *
     * @param int $themeId
     * @return Iterator
     */
    public static function findByTheme(int $themeId): Iterator
    {
        return self::fetchByTheme($themeId, 'theme_form', new self());
    }

    public function format(): array
    {
        return [
            'name' => $this->name,
            'form' => $this->getForm()->format(),
        ];
    }

    /**
     * Gets the form of the theme form
     *
     * @return Form
     */
    public function getForm(): Form
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