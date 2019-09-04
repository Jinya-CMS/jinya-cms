<?php

/** @noinspection HtmlRequiredTitleElement */

/** @noinspection HtmlRequiredLangAttribute */

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form\Form;
use Jinya\Framework\Events\Mailing\MailerEvent;
use Psr\Log\LoggerInterface;
use Swift_Mailer;
use Swift_Message;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MailerService implements MailerServiceInterface
{
    /** @var Swift_Mailer */
    private $swift;

    /** @var string */
    private $mailerSender;
    /** @noinspection PhpUndefinedClassInspection */

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var LoggerInterface */
    private $logger;
    /** @noinspection PhpUndefinedClassInspection */
    /** @noinspection PhpUndefinedClassInspection */

    /**
     * MailerService constructor.
     * @param Swift_Mailer $swift
     * @param string $mailerSender
     * @param EventDispatcherInterface $eventDispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(
        Swift_Mailer $swift,
        string $mailerSender,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->swift = $swift;
        $this->mailerSender = $mailerSender;
        $this->eventDispatcher = $eventDispatcher;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function sendMail(Form $form, array $data): array
    {
        $pre = $this->eventDispatcher->dispatch(new MailerEvent($form, $data), MailerEvent::PRE_SEND_MAIL);
        if (!$pre->isCancel()) {
            $this->logger->info('Send message to ' . $form->getToAddress());
            /** @var Swift_Message $message */
            $message = $this->swift->createMessage();
            $message->addTo($form->getToAddress());
            $message->setSubject('Form ' . $form->getTitle() . ' submitted');
            $message->setBody($this->formatBody($data), 'text/html');
            $message->setFrom($this->mailerSender);
            $failedRecipients = [];
            $this->swift->send($message, $failedRecipients);
            if (!empty($failedRecipients)) {
                $this->logger->error("Couldn't send message for recipients", $failedRecipients);
            }

            $this->eventDispatcher->dispatch(new MailerEvent($form, $data), MailerEvent::POST_SEND_MAIL);

            return $failedRecipients;
        }

        return [];
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

        return $body . '</table></body></html>';
    }

    /**
     * Gets the body for the given form and data
     *
     * @param Form $form
     * @param array $data
     * @return string
     */
    public function getBody(Form $form, array $data): string
    {
        return $this->formatBody($data);
    }
}
