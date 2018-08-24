<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 23.08.18
 * Time: 21:37
 */

namespace Jinya\Framework\Events\Form;

use Symfony\Component\EventDispatcher\Event;

class FormItemGetItemEvent extends Event
{
    public const PRE_GET = 'FormItemPreGet';

    /** @var string */
    private $formSlug;

    /** @var int */
    private $position;

    /**
     * FormItemGetItemEvent constructor.
     * @param string $formSlug
     * @param int $position
     */
    public function __construct(string $formSlug, int $position)
    {
        $this->formSlug = $formSlug;
        $this->position = $position;
    }

    /**
     * @return string
     */
    public function getFormSlug(): string
    {
        return $this->formSlug;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }
}
