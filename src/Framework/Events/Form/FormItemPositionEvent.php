<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 19:48
 */

namespace Jinya\Framework\Events\Form;

use Jinya\Entity\Form\Form;
use Jinya\Entity\Form\FormItem;
use Jinya\Framework\Events\Common\CancellableEvent;

class FormItemPositionEvent extends CancellableEvent
{
    public const PRE_UPDATE = 'FormItemPositionPreUpdate';

    public const POST_UPDATE = 'FormItemPositionPostUpdate';

    /** @var Form */
    private Form $form;

    /** @var FormItem */
    private FormItem $formItem;

    /** @var int */
    private int $oldPosition;

    /** @var int */
    private int $newPosition;

    /**
     * FormItemPositionEvent constructor.
     */
    public function __construct(Form $form, FormItem $formItem, int $oldPosition, int $newPosition)
    {
        $this->form = $form;
        $this->formItem = $formItem;
        $this->oldPosition = $oldPosition;
        $this->newPosition = $newPosition;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getFormItem(): FormItem
    {
        return $this->formItem;
    }

    public function getOldPosition(): int
    {
        return $this->oldPosition;
    }

    public function getNewPosition(): int
    {
        return $this->newPosition;
    }
}
