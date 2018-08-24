<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 23.08.18
 * Time: 19:09
 */

namespace Jinya\Framework\Events\Form;

use Jinya\Entity\Form\FormItem;
use Jinya\Framework\Events\Common\CancellableEvent;

class FormItemEvent extends CancellableEvent
{
    public const PRE_ADD = 'FormItemPreAdd';

    public const POST_ADD = 'FormItemPostAdd';

    public const PRE_UPDATE = 'FormItemPreUpdate';

    public const POST_UPDATE = 'FormItemPostUpdate';

    public const POST_GET = 'FormItemPostGet';

    public const PRE_DELETE = 'FormItemPreDelete';

    public const POST_DELETE = 'FormItemPostDelete';

    /** @var FormItem */
    private $formItem;

    /**
     * FormItemEvent constructor.
     * @param FormItem $formItem
     */
    public function __construct(FormItem $formItem)
    {
        $this->formItem = $formItem;
    }

    /**
     * @return FormItem
     */
    public function getFormItem(): FormItem
    {
        return $this->formItem;
    }
}
