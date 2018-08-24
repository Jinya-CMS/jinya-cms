<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 20.08.18
 * Time: 09:41
 */

namespace Jinya\Framework\Events\Forms;

use Jinya\Entity\Form\Form;
use Jinya\Framework\Events\Common\CancellableEvent;

class FormEvent extends CancellableEvent
{
    public const PRE_SAVE = 'FormPreSave';
    public const POST_SAVE = 'FormPostSave';
    public const PRE_GET = 'FormPreGet';
    public const POST_GET = 'FormPostGet';
    public const PRE_DELETE = 'FormPreDelete';
    public const POST_DELETE = 'FormPostDelete';

    /** @var Form */
    private $form;

    /** @var string */
    private $slug;

    /**
     * FormEvent constructor.
     * @param Form $form
     * @param string $slug
     */
    public function __construct(?Form $form, string $slug)
    {
        $this->form = $form;
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @return Form
     */
    public function getForm(): ?Form
    {
        return $this->form;
    }
}