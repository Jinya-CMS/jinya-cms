<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:48
 */

namespace Jinya\Framework\Events\Artworks;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;
use Jinya\Framework\Events\Common\CancellableEvent;

class FormItemPositionEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'FormItemPositionPreUpdate';

    public const POST_UPDATE = 'FormItemPositionPostUpdate';

    /** @var Form */
    private $form;

    /** @var FormItem */
    private $formItem;

    /** @var int */
    private $oldPosition;

    /** @var int */
    private $newPosition;

    /**
     * FormItemPositionEvent constructor.
     * @param Form $form
     * @param FormItem $formItem
     * @param int $oldPosition
     * @param int $newPosition
     */
    public function __construct(Form $form, FormItem $formItem, int $oldPosition, int $newPosition)
    {
        $this->form = $form;
        $this->formItem = $formItem;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return FormItem
     */
    public function getFormItem(): FormItem
    {
        return $this->formItem;
    }

    /**
     * @return int
     */
    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    /**
     * @return int
     */
    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
