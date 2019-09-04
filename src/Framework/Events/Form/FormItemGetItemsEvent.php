<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 23.08.18
 * Time: 19:15
 */

namespace Jinya\Framework\Events\Form;

use Jinya\Entity\Form\FormItem;
use Symfony\Contracts\EventDispatcher\Event;

class FormItemGetItemsEvent extends Event
{
    public const PRE_GET_ITEMS = 'FormItemsPreGetItems';

    public const POST_GET_ITEMS = 'FormItemsPostGetItems';

    /** @var string */
    private $formSlug;

    /** @var FormItem[] */
    private $items;

    /**
     * FormItemGetItemsEvent constructor.
     * @param string $formSlug
     * @param FormItem[] $items
     */
    public function __construct(string $formSlug, array $items)
    {
        $this->formSlug = $formSlug;
        $this->items = $items;
    }

    /**
     * @return string
     */
    public function getFormSlug(): string
    {
        return $this->formSlug;
    }

    /**
     * @return FormItem[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
