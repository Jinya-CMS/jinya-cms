<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 11.11.2017
 * Time: 15:12
 */

namespace Jinya\EventSubscriber;

use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Jinya\Entity\Base\HistoryEnabledEntity;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use function strtolower;

class HistoryEnabledEntitySubscriber implements EventSubscriber
{
    private TokenStorageInterface $tokenStorage;

    /**
     * HistoryEnabledEntitySubscriber constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [
            Events::prePersist,
            Events::preUpdate,
            Events::postLoad,
        ];
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs): void
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $changeSet = $eventArgs->getEntityChangeSet();
            $entity = $eventArgs->getEntity();
            if (($entity instanceof HistoryEnabledEntity) && !$this->checkOnlyUpdatedFieldsChanged($changeSet)) {
                $lastUpdatedBy = $entity->getUpdatedBy();
                $entity->setLastUpdatedAt(new DateTime());
                /* @noinspection PhpParamsInspection */
                $entity->setUpdatedBy($token->getUser());
                $history = $entity->getHistory();
                $changeSet['lastUpdatedAt'][1] = $entity->getLastUpdatedAt();
                $changeSet['updatedBy'] = [$lastUpdatedBy, $entity->getUpdatedBy()];
                $history[] = [
                    'entry' => $changeSet,
                    'timestamp' => $entity->getLastUpdatedAt()->format('c'),
                    'initial' => false,
                ];
                $entity->setHistory($history);
            }
        }
    }

    private function checkOnlyUpdatedFieldsChanged(array $changeSet): bool
    {
        foreach ($changeSet as $key => $item) {
            $lowerKey = strtolower($key);

            if ('lastupdatedat' !== $lowerKey && 'updatedby' !== $lowerKey && 'history' !== $lowerKey) {
                return false;
            }
        }

        return true;
    }

    public function postLoad(LifecycleEventArgs $eventArgs): void
    {
        $token = $this->tokenStorage->getToken();
        if (null !== $token) {
            $entity = $eventArgs->getEntity();
            if ($entity instanceof HistoryEnabledEntity) {
                $entity->setLastUpdatedAt(new DateTime());
                /* @noinspection PhpParamsInspection */
                $entity->setUpdatedBy($token->getUser());
            }
        }
    }

    public function prePersist(LifecycleEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getEntity();
        if ($entity instanceof HistoryEnabledEntity && null === $entity->getCreatedAt()) {
            $entity->setCreatedAt(new DateTime());
            $entity->setLastUpdatedAt(new DateTime());
            /* @noinspection PhpParamsInspection */
            /* @noinspection NullPointerExceptionInspection */
            $entity->setCreator($this->tokenStorage->getToken()->getUser());
            /* @noinspection PhpParamsInspection */
            /* @noinspection NullPointerExceptionInspection */
            $entity->setUpdatedBy($this->tokenStorage->getToken()->getUser());
            $historyEntry = $entity->jsonSerialize();
            $historyEntry = array_map(static function ($item) {
                return [null, $item];
            }, $historyEntry);
            unset($historyEntry['history']);
            $entity->setHistory([
                [
                    'entry' => $historyEntry,
                    'timestamp' => $entity->getLastUpdatedAt()->format('c'),
                    'initial' => true,
                ],
            ]);
        }
    }
}
