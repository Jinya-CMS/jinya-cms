<?php
/**
 * Created by PhpStorm.
 * User: imanuel
 * Date: 29.08.18
 * Time: 21:02
 */

namespace Jinya\EventSubscriber\Cache;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\Menu\Menu;
use Jinya\Entity\Menu\MenuItem;
use Jinya\Framework\Events\Menu\MenuFillFromArrayEvent;
use Jinya\Framework\Events\Menu\MenuItemAddEvent;
use Jinya\Framework\Events\Menu\MenuItemRemoveEvent;
use Jinya\Framework\Events\Menu\MenuItemUpdateEvent;
use Jinya\Framework\Events\Menu\MenuSaveEvent;
use Jinya\Services\Cache\CacheBuilderInterface;
use Jinya\Services\Configuration\ConfigurationServiceInterface;
use Jinya\Services\Menu\MenuItemServiceInterface;
use Jinya\Services\Menu\MenuService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class MenuCacheSubscriber implements EventSubscriberInterface
{
    /** @var ConfigurationServiceInterface */
    private ConfigurationServiceInterface $configService;

    /** @var CacheBuilderInterface */
    private CacheBuilderInterface $cacheBuilder;

    /** @var MenuService */
    private MenuService $menuService;

    /** @var EntityManagerInterface */
    private EntityManagerInterface $entityManager;

    /**
     * MenuCacheSubscriber constructor.
     * @param ConfigurationServiceInterface $configService
     * @param CacheBuilderInterface $cacheBuilder
     * @param MenuService $menuService
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        ConfigurationServiceInterface $configService,
        CacheBuilderInterface $cacheBuilder,
        MenuService $menuService,
        EntityManagerInterface $entityManager
    ) {
        $this->configService = $configService;
        $this->cacheBuilder = $cacheBuilder;
        $this->menuService = $menuService;
        $this->entityManager = $entityManager;
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
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            MenuSaveEvent::POST_SAVE => 'onMenuSave',
            MenuItemUpdateEvent::POST_UPDATE => 'onMenuItemUpdate',
            MenuItemAddEvent::POST_ADD => 'onMenuItemAdd',
            MenuItemRemoveEvent::POST_REMOVE => 'onMenuItemRemove',
            MenuFillFromArrayEvent::POST_FILL_FROM_ARRAY => 'onMenuFillFromArray',
        ];
    }

    public function onMenuFillFromArray(MenuFillFromArrayEvent $event): void
    {
        $menu = $this->menuService->get($event->getId());
        $this->compileCache($menu);
    }

    private function compileCache(Menu $menu): void
    {
        $config = $this->configService->getConfig();
        $menus = $config->getCurrentTheme()->getMenus();
        $menuAffected = false;
        foreach ($menus as $item) {
            if ($item->getMenu()->getId() === $menu->getId()) {
                $menuAffected = true;

                break;
            }
        }

        if ($menuAffected) {
            @$this->cacheBuilder->clearCache();
            $this->cacheBuilder->buildCache();
        }
    }

    public function onMenuItemAdd(MenuItemAddEvent $event): void
    {
        if (MenuItemServiceInterface::MENU === $event->getType()) {
            $menu = $this->menuService->get($event->getParentId());
        } else {
            $menu = $this->findMenu($event->getParentId());
        }
        $this->compileCache($menu);
    }

    public function onMenuItemRemove(MenuItemRemoveEvent $event): void
    {
        if (MenuItemServiceInterface::MENU === $event->getType()) {
            $menu = $this->menuService->get($event->getParentId());
        } else {
            $menu = $this->findMenu($event->getParentId());
        }
        $this->compileCache($menu);
    }

    private function findMenu(int $id): Menu
    {
        $ids = $this->entityManager
            ->createQueryBuilder()
            ->select(['parent.id AS parentId', 'menu.id AS menuId'])
            ->from(MenuItem::class, 'item')
            ->join('item.parent', 'parent')
            ->join('item.menu', 'menu')
            ->where('item.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();

        if ($ids['menuId']) {
            return $this->menuService->get($ids['menuId']);
        }

        return $this->findMenu($ids['parentId']);
    }

    public function onMenuItemUpdate(MenuItemUpdateEvent $event): void
    {
        if ($event->getItem()->getMenu()) {
            $menu = $event->getItem()->getMenu();
        } else {
            $menu = $this->findMenu($event->getItem()->getId());
        }

        $this->compileCache($menu);
    }

    public function onMenuSave(MenuSaveEvent $event): void
    {
        $this->compileCache($event->getMenu());
    }
}
