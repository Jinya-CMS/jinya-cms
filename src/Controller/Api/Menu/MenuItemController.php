<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 18:59
 */

namespace Jinya\Controller\Api\Menu;

use Jinya\Entity\Menu\MenuItem;
use Jinya\Entity\Menu\RoutingEntry;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Menu\MenuFormatterInterface;
use Jinya\Formatter\Menu\MenuItemFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Menu\MenuItemServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_key_exists;
use function array_map;

class MenuItemController extends BaseApiController
{
    /**
     * @Route("/api/menu/{id}/items", methods={"GET"}, name="api_menu_items_get_all")
     *
     * @param int $id
     * @param MenuItemServiceInterface $menuItemService
     * @param MenuItemFormatterInterface $menuItemFormatter
     * @return Response
     */
    public function getAllAction(
        int $id,
        MenuItemServiceInterface $menuItemService,
        MenuItemFormatterInterface $menuItemFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $menuItemService, $menuItemFormatter) {
            return array_map(static function ($item) use ($menuItemFormatter) {
                return $menuItemFormatter
                    ->init($item)
                    ->id()
                    ->title()
                    ->position()
                    ->route()
                    ->children()
                    ->pageType()
                    ->highlighted()
                    ->format();
            }, $menuItemService->getAll($id, MenuItemServiceInterface::MENU));
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}/items/{position}", methods={"GET"}, name="api_menu_items_get")
     *
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @param MenuItemFormatterInterface $menuItemFormatter
     * @return Response
     */
    public function getAction(
        int $id,
        int $position,
        MenuItemServiceInterface $menuItemService,
        MenuItemFormatterInterface $menuItemFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(
            static function () use ($id, $position, $menuItemService, $menuItemFormatter) {
                return $menuItemFormatter
                    ->init($menuItemService->get($id, $position, MenuItemServiceInterface::MENU))
                    ->title()
                    ->id()
                    ->route()
                    ->parent()
                    ->highlighted()
                    ->position()
                    ->children()
                    ->format();
            }
        );

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}/items/{position}", methods={"POST"}, name="api_menu_items_post_parent_menu")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @param MenuFormatterInterface $menuFormatter
     * @param MenuItemFormatterInterface $menuItemFormatter
     * @return Response
     */
    public function postMenuParentAction(
        int $id,
        int $position,
        MenuItemServiceInterface $menuItemService,
        MenuFormatterInterface $menuFormatter,
        MenuItemFormatterInterface $menuItemFormatter
    ): Response {
        [$data, $status] = $this->tryExecute($this->addItem(
            MenuItemServiceInterface::MENU,
            $id,
            $position,
            $menuItemService,
            $menuFormatter,
            $menuItemFormatter
        ), Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * Adds the item
     *
     * @param string $type
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @param MenuFormatterInterface $menuFormatter
     * @param MenuItemFormatterInterface $menuItemFormatter
     * @return callable
     */
    private function addItem(
        string $type,
        int $id,
        int $position,
        MenuItemServiceInterface $menuItemService,
        MenuFormatterInterface $menuFormatter,
        MenuItemFormatterInterface $menuItemFormatter
    ): callable {
        return function () use ($menuFormatter, $menuItemService, $menuItemFormatter, $id, $position, $type) {
            $title = $this->getValue('title');
            $pageType = $this->getValue('pageType');
            $highlighted = $this->getValue('highlighted', false);
            $routeJson = $this->getValue('route');

            $missingFields = [];
            if (empty($routeJson)) {
                $missingFields['route'] = 'api.menu.item.field.route.missing';
            } else {
                if (!array_key_exists('url', $routeJson)) {
                    $missingFields['route_url'] = 'api.menu.item.field.route.url.missing';
                }
                if (!array_key_exists('name', $routeJson)) {
                    $missingFields['route_name'] = 'api.menu.item.field.route.name.missing';
                }
            }
            if (empty($pageType)) {
                $missingFields['pageType'] = 'api.menu.item.field.pageType.missing';
            }
            if (empty($title)) {
                $missingFields['title'] = 'api.menu.item.field.title.missing';
            }

            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $route = new RoutingEntry();
            $item = new MenuItem();

            $route->setUrl($routeJson['url']);
            $route->setMenuItem($item);
            if (array_key_exists('parameter', $routeJson)) {
                $route->setRouteParameter($routeJson['parameter']);
            } else {
                $route->setRouteParameter([]);
            }
            $route->setRouteName($routeJson['name']);

            $item->setPosition($position);
            $item->setTitle($title);
            $item->setPageType($pageType);
            $item->setHighlighted($highlighted);
            $item->setRoute($route);

            $menuItemService->addItem($id, $item, $type);

            if (MenuItemServiceInterface::MENU === $type) {
                return $menuFormatter
                    ->init($item->getMenu())
                    ->name()
                    ->items()
                    ->format();
            }

            return $menuItemFormatter
                ->init($item->getParent())
                ->title()
                ->route()
                ->children()
                ->format();
        };
    }

    /**
     * @Route("/api/menu/{_}/items/{id}/{position}", methods={"POST"}, name="api_menu_items_post_parent_menu_item")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @param MenuFormatterInterface $menuFormatter
     * @param MenuItemFormatterInterface $menuItemFormatter
     * @return Response
     */
    public function postMenuItemParentAction(
        int $id,
        int $position,
        MenuItemServiceInterface $menuItemService,
        MenuFormatterInterface $menuFormatter,
        MenuItemFormatterInterface $menuItemFormatter
    ): Response {
        [$data, $status] = $this->tryExecute($this->addItem(
            MenuItemServiceInterface::PARENT,
            $id,
            $position,
            $menuItemService,
            $menuFormatter,
            $menuItemFormatter
        ), Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}/items/{position}", methods={"DELETE"}, name="api_menu_items_delete_parent_menu")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @return Response
     */
    public function deleteMenuParentAction(int $id, int $position, MenuItemServiceInterface $menuItemService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $position, $menuItemService) {
            $menuItemService->removeItem($id, $position, MenuItemServiceInterface::MENU);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{_}/items/{id}/{position}", methods={"DELETE"}, name="api_menu_items_delete_parent_menu_item")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $position
     * @param MenuItemServiceInterface $menuItemService
     * @return Response
     */
    public function deleteMenuItemParentAction(
        int $id,
        int $position,
        MenuItemServiceInterface $menuItemService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $position, $menuItemService) {
            $menuItemService->removeItem($id, $position, MenuItemServiceInterface::PARENT);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}/items/batch", methods={"PUT"}, name="api_menu_items_batch")
     * @IsGranted("ROLE_WRITER")
     *
     * @param Request $request
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function batchChangeItems(Request $request, int $id, MenuServiceInterface $menuService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($request, $id, $menuService) {
            $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $menuService->fillFromArray($id, $data);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
