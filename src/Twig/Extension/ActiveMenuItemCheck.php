<?php

namespace Jinya\Twig\Extension;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Entity\Menu\RoutingEntry;
use Symfony\Component\Routing\RequestContext;
use Twig_Extension;
use Twig_Function;

class ActiveMenuItemCheck extends Twig_Extension
{
    /** @var RequestContext */
    private $requestContext;

    /** @var EntityManagerInterface */
    private $entityManager;

    /**
     * ActiveMenuItemCheck constructor.
     * @param RequestContext $requestContext
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(RequestContext $requestContext, EntityManagerInterface $entityManager)
    {
        $this->requestContext = $requestContext;
        $this->entityManager = $entityManager;
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
        $pathInfo = array_key_exists('active', $context) ? $context['active'] : $this->requestContext->getPathInfo();
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
    }

    private function findMenuForEntry(MenuItem $menuItem): Menu
    {
        if ($menuItem->getMenu()) {
            return $menuItem->getMenu();
        } else {
            return $this->findMenuForEntry($menuItem->getParent());
        }
    }
}
