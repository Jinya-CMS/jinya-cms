<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.01.2018
 * Time: 19:02
 */

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form;
use Swift_Mailer;
use Swift_Message;

class MailerService implements MailerServiceInterface
{
    /** @var Swift_Mailer */
    private $swift;

    /** @var string */
    private $mailerSender;

    /**
     * MailerService constructor.
     * @param Swift_Mailer $swift
     * @param string $mailerSender
     */
    public function __construct(Swift_Mailer $swift, string $mailerSender)
    {
        $this->swift = $swift;
        $this->mailerSender = $mailerSender;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Form $form, array $data): void
    {
        /** @var Swift_Message $message */
        $message = $this->swift->createMessage('message');
        $message->addTo($form->getToAddress());
        $message->setSubject('Form '.$form->getTitle().' submitted');
        $message->setBody($this->formatBody($data), 'text/html');
        $message->setFrom($this->mailerSender);
        $this->swift->send($message);
    }

    private function formatBody(array $data): string
    {
        $body = '<html><head></head><body><table>';

        foreach ($data as $key => $item) {
            $body .= "<tr>
                        <td>$key</td>
                        <td>$item</td>
                      </tr>";
        }

        $body .= '</table></body></html>';

        return $body;
    }
}
