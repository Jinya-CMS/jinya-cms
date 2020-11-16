<?php

namespace Jinya\EventSubscriber\MessagingCenter;

use Jinya\Entity\Form\FormItem;
use Jinya\Entity\Form\Message;
use Jinya\Framework\Events\Mailing\MailerEvent;
use Jinya\Framework\Events\Priority;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Form\MessageServiceInterface;
use Jinya\Services\Mailing\MailerServiceInterface;
use Jinya\Services\Mailing\SpamDetectorInterface;
use Jinya\Services\Slug\SlugServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RedirectMailsEventSubscriber implements EventSubscriberInterface
{
    private ConfigurationServiceInterface $configService;

    private SpamDetectorInterface $spamDetector;

    private MessageServiceInterface $messageService;

    private MailerServiceInterface $mailerService;

    private SlugServiceInterface $slugService;

    /**
     * RedirectMailsEventSubscriber constructor.
     */
    public function __construct(
        ConfigurationServiceInterface $configService,
        SpamDetectorInterface $spamDetector,
        MessageServiceInterface $messageService,
        MailerServiceInterface $mailerService,
        SlugServiceInterface $slugService
    ) {
        $this->configService = $configService;
        $this->spamDetector = $spamDetector;
        $this->messageService = $messageService;
        $this->mailerService = $mailerService;
        $this->slugService = $slugService;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MailerEvent::PRE_SEND_MAIL => ['preSendMail', Priority::MEDIUM],
        ];
    }

    public function preSendMail(MailerEvent $event): MailerEvent
    {
        if ($this->configService->getConfig()->isMessagingCenterEnabled()) {
            $event->setCancel(true);

            $form = $event->getForm();
            $data = $event->getData();

            $message = new Message();
            $message->setForm($form);
            $message->setTargetAddress($form->getToAddress());
            $message->setSpam($this->spamDetector->checkForSpam($form, $data));
            $message->setContent($this->mailerService->getBody($form, $data));
            foreach ($form->getItems() as $item) {
                /** @var FormItem $item */
                $options = $item->getOptions();
                if (array_key_exists('from_address', $options) && $options['from_address']) {
                    $message->setFromAddress($data[strtolower($this->slugService->generateSlug($item->getLabel()))]);
                }
                if (array_key_exists('subject', $options) && $options['subject']) {
                    $message->setSubject($data[strtolower($this->slugService->generateSlug($item->getLabel()))]);
                }
            }

            $this->messageService->saveOrUpdate($message);
        }

        return $event;
    }
}
