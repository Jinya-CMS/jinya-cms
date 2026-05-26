<?php

namespace Jinya\Cms\Database;

use JetBrains\PhpStorm\ArrayShape;
use Jinya\Database\Attributes\Column;
use Jinya\Database\Attributes\Table;
use Jinya\Database\Creatable;
use Jinya\Database\Deletable;
use Jinya\Database\DeletableEntityTrait;
use Jinya\Database\EntityTrait;
use Jinya\Database\Updatable;

/**
 * This class contains a form connected to a theme
 */
#[Table('theme_form')]
class ThemeForm implements Creatable, Updatable, Deletable
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

    /** @var int The form ID */
    #[Column(sqlName: 'form_id')]
    public int $formId = -1;

    private static function getLinkIdProperty(): string
    {
        return 'formId';
    }

    private static function getLinkIdColumn(): string
    {
        return 'form_id';
    }

    /**
     * Formats the theme form into an array
     *
     * @return array<string, array<string, array<string, array<string, string|null>|string>|int|string>|string|null>
     */
    #[ArrayShape(['name' => 'string', 'form' => 'array'])]
    public function format(): array
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
     */
    public function getForm(): ?Form
    {
        return Form::findById($this->formId);
    }
}
