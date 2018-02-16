<?php

namespace Jinya\Controller\Designer;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Jinya\Entity\Menu;
use Jinya\Entity\MenuItem;
use Jinya\Entity\RoutingEntry;
use Jinya\Framework\BaseController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function is_array;

class MenuController extends BaseController
{
    /**
     * @Route("/designer/menu", name="designer_menu_index")
     *
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function indexAction(MenuServiceInterface $menuService): Response
    {
        $menus = $menuService->getAll();

        return $this->render('@Designer/menu/index.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @Route("/designer/menu/add", name="designer_menu_add")
     *
     * @return Response
     */
    public function addAction(): Response
    {
        return $this->render('@Designer/menu/add.html.twig');
    }

    /**
     * @Route("/designer/menu/api/{id}", name="designer_menu_save_with_id", methods={"PUT", "POST"})
     * @Route("/designer/menu/api/", name="designer_menu_save_without_id", methods={"POST"})
     *
     * @param int $id
     * @param Request $request
     * @param MenuServiceInterface $menuService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function saveMenuAction(int $id = -1, Request $request, MenuServiceInterface $menuService, MediaServiceInterface $mediaService): Response
    {
        if ($id != -1) {
            $menu = $menuService->get($id);
            $menu->getMenuItems()->clear();
            $menuService->save($menu);
        } else {
            $menu = new Menu();
        }

        $postedMenu = json_decode($request->get('_menu'), true);

        if ($request->files->has('_logo')) {
            /** @var UploadedFile $logoFile */
            $logoFile = $request->files->get('_logo');

            $logo = $mediaService->saveMedia($logoFile, MediaServiceInterface::MENU_LOGO);
            $menu->setLogo($logo);
        }

        $menu->setName($postedMenu['_name']);
        $menu->setMenuItems($this->prepareMenuChildren($postedMenu['_children'], $menu));

        try {
            $menu = $menuService->save($menu);
            $router = $this->get('router');

            return $this->json([
                'success' => true,
                'redirectTarget' => $router->generate('designer_menu_details', ['id' => $menu->getId()])
            ]);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 400);
        }
    }

    /**
     * @param array $children
     * @param Menu|null $menu
     * @param MenuItem|null $parent
     * @return ArrayCollection
     */
    private function prepareMenuChildren(array $children, ?Menu $menu = null, ?MenuItem $parent = null): ArrayCollection
    {
        $items = [];
        foreach ($children as $child) {
            $item = new MenuItem();
            $route = new RoutingEntry();
            $route->setRouteName($child['_route']['_name']);
            if (is_array($child['_route']['_parameter'])) {
                $route->setRouteParameter($child['_route']['_parameter']);
            } else {
                $route->setRouteParameter([]);
            }

            $route->setUrl($child['_displayUrl']);
            $route->setMenuItem($item);

            $item->setTitle($child['_title']);
            $item->setMenu($menu);
            $item->setParent($parent);
            $item->setRoute($route);
            $item->setPageType($child['_pageType']);
            $item->setHighlighted($child['_highlighted']);
            $item->setChildren($this->prepareMenuChildren($child['_children'], null, $item));

            $items[] = $item;
        }

        return new ArrayCollection($items);
    }

    /**
     * @Route("/designer/menu/{id}", name="designer_menu_details", methods={"GET"})
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function detailsAction(int $id, MenuServiceInterface $menuService): Response
    {
        return $this->render('@Designer/menu/details.html.twig', [
            'menu' => $menuService->get($id)
        ]);
    }

    /**
     * @Route("/designer/menu/{id}/edit", name="designer_menu_edit")
     *
     * @param int $id
     * @return Response
     */
    public function editAction(int $id, MenuServiceInterface $menuService): Response
    {
        return $this->render('@Designer/menu/edit.html.twig', [
            'menu' => $menuService->get($id)
        ]);
    }

    /**
     * @Route("/designer/menu/{id}", name="designer_menu_delete", methods={"DELETE"})
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function deleteAction(int $id, MenuServiceInterface $menuService): Response
    {
        try {
            $menuService->delete($id);

            return $this->json(['success' => true]);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    /**
     * @Route("/designer/menu/api/{id}", name="designer_menu_load_menu", methods={"GET"})
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function loadAction(int $id, MenuServiceInterface $menuService): Response
    {
        try {
            return $this->json($menuService->get($id));
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
