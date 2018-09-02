<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 24.08.18
 * Time: 18:18
 */

namespace Jinya\Framework\Events\Mailing;

use Jinya\Entity\Form\Form;
use Jinya\Framework\Events\Common\CancellableEvent;

class MailerEvent extends CancellableEvent
{
    public const PRE_SEND_MAIL = 'MailerPreSendMail';

    public const POST_SEND_MAIL = 'MailerPostSendMail';

    /** @var Form */
    private $form;

    /** @var array */
    private $data;

    /**
     * MailerEvent constructor.
     * @param Form $form
     * @param array $data
     */
    public function __construct(Form $form, array $data)
    {
        $this->form = $form;
        $this->data = $data;
    }

    /**
     * @return Form
     */
    public function getForm(): Form
    {
        return $this->form;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
