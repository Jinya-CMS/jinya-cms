<?php

namespace Jinya\Services\Mailing;

use Jinya\Entity\Form\Form;
use Jinya\Framework\Events\Mailing\MailerEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class MailerService implements MailerServiceInterface
{
    /** @var MailerInterface */
    private MailerInterface $mailer;

    /** @var string */
    private string $mailerSender;

    /** @var EventDispatcherInterface */
    private EventDispatcherInterface $eventDispatcher;

    /** @var LoggerInterface */
    private LoggerInterface $logger;

    /**
     * MailerService constructor.
     */
    public function __construct(
        MailerInterface $mailer,
        string $mailerSender,
        EventDispatcherInterface $eventDispatcher,
        LoggerInterface $logger
    ) {
        $this->mailer = $mailer;
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
            $message = new Email();
            $message->to($form->getToAddress())
                ->subject('Form ' . $form->getTitle() . ' submitted')
                ->html($this->formatBody($data))
                ->from($this->mailerSender);
            $failedRecipients = [];

            try {
                $this->mailer->send($message);
            } catch (TransportExceptionInterface $e) {
                $this->logger->error('Failed to send message');
                $this->logger->error($e);
            }

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
     */
    public function getBody(Form $form, array $data): string
    {
        return $this->formatBody($data);
    }
}
