<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 23.08.18
 * Time: 21:37
 */

namespace Jinya\Framework\Events\Form;

use Symfony\Contracts\EventDispatcher\Event;

class FormItemGetItemEvent extends Event
{
    public const PRE_GET = 'FormItemPreGet';

    /** @var string */
    private string $formSlug;

    /** @var int */
    private int $position;

    /**
     * FormItemGetItemEvent constructor.
     */
    public function __construct(string $formSlug, int $position)
    {
        $this->formSlug = $formSlug;
        $this->position = $position;
    }

    public function getFormSlug(): string
    {
        return $this->formSlug;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
