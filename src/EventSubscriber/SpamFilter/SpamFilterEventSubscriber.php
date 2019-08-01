<?php

namespace Jinya\EventSubscriber\SpamFilter;

use Jinya\Entity\Form\FormItem;
use Jinya\Framework\Events\Mailing\MailerEvent;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SpamFilterEventSubscriber implements EventSubscriberInterface
{
    /** @var ConfigurationServiceInterface */
    private $configurationService;

    /**
     * SpamFilterEventSubscriber constructor.
     * @param ConfigurationServiceInterface $configurationService
     */
    public function __construct(ConfigurationServiceInterface $configurationService)
    {
        $this->configurationService = $configurationService;
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
            MailerEvent::PRE_SEND_MAIL => ['checkForSpam']
        ];
    }

    public function checkForSpam(MailerEvent $event): MailerEvent
    {
        $form = $event->getForm();
        $data = $event->getData();

        foreach ($form->getItems()->toArray() as $item) {
            /** @var FormItem $item */
            foreach ($item->getSpamFilter() as $keyword) {
                $position = stripos($data[lcfirst($item->getLabel())], $keyword);
                if ($position !== false && $position !== -1) {
                    $event->setCancel(true);
                }
            }
        }

        return $event;
    }
}
