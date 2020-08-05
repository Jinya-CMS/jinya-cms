<?php

namespace Jinya\EventSubscriber\SpamFilter;

use Jinya\Framework\Events\Mailing\MailerEvent;
use Jinya\Framework\Events\Priority;
use Jinya\Services\Mailing\SpamDetectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SpamFilterEventSubscriber implements EventSubscriberInterface
{
    private SpamDetectorInterface $spamDetector;

    /**
     * SpamFilterEventSubscriber constructor.
     */
    public function __construct(SpamDetectorInterface $spamDetector)
    {
        $this->spamDetector = $spamDetector;
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
            MailerEvent::PRE_SEND_MAIL => ['checkForSpam', Priority::LOWEST],
        ];
    }

    public function checkForSpam(MailerEvent $event): MailerEvent
    {
        $form = $event->getForm();
        $data = $event->getData();
        $event->setCancel($this->spamDetector->checkForSpam($form, $data));

        return $event;
    }
}
