<?php

namespace Jinya\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Entity\Menu\RoutingEntry;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\RequestContext;
use Throwable;
use Twig_Extension;
use Twig_Function;

class ActiveMenuItemCheck extends Twig_Extension
{
    /** @var RequestContext */
    private $requestContext;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ActiveMenuItemCheck constructor.
     * @param RequestContext $requestContext
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        RequestContext $requestContext,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ) {
        $this->requestContext = $requestContext;
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'active_menu_item_check';
    }

    public function getFunctions()
    {
        return [
            'isActiveMenuItem' => new Twig_Function('isActiveMenuItem', [$this, 'isActiveMenuItem'], [
                'needs_context' => true,
            ]),
            'isChildActiveMenuItem' => new Twig_Function('isChildActiveMenuItem', [$this, 'isChildActiveMenuItem'], [
                'needs_context' => true,
            ]),
            'getActiveMenuItem' => new Twig_Function('getActiveMenuItem', [$this, 'getActiveMenuItem'], [
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * @param array $context
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isChildActiveMenuItem(array $context, MenuItem $menuItem): bool
    {
        foreach ($menuItem->getChildren()->toArray() as $item) {
            if ($this->isActiveMenuItem($context, $item) || $this->isChildActiveMenuItem($context, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $context
     * @param MenuItem $menuItem
     * @return bool
     */
    public function isActiveMenuItem(array $context, MenuItem $menuItem): bool
    {
        $url = $menuItem->getRoute()->getUrl();
        $pathInfo = $this->requestContext->getPathInfo();

        if (array_key_exists('active', $context) && $context['active'] == $url) {
            return true;
        }

        return $url === $pathInfo;
    }

    public function getActiveMenuItem(array $context)
    {
        try {
            $pathInfo = array_key_exists(
                'active',
                $context
            ) ? $context['active'] : $this->requestContext->getPathInfo();
            $relevantEntries = $this->entityManager->getRepository(RoutingEntry::class)->findBy(['url' => $pathInfo]);

            /** @var Menu $primaryMenu */
            $primaryMenu = $context['theme']['active']->getPrimaryMenu();
            foreach ($relevantEntries as $relevantEntry) {
                $menu = $this->findMenuForEntry($relevantEntry->getMenuItem());
                if ($menu->getId() === $primaryMenu->getId()) {
                    return $relevantEntry->getMenuItem();
                }
            }

            return null;
        } catch (Throwable $exception) {
            $this->logger->warning($exception->getMessage());
            $this->logger->warning($exception->getTraceAsString());

            return null;
        }
    }

    private function findMenuForEntry(MenuItem $menuItem): Menu
    {
        if ($menuItem->getMenu()) {
            return $menuItem->getMenu();
        }

        return $this->findMenuForEntry($menuItem->getParent());
    }
}
