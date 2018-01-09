<?php

namespace DesignerBundle\Controller;

use DataBundle\Entity\Menu;
use DataBundle\Entity\MenuItem;
use DataBundle\Entity\RoutingEntry;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function is_array;

class MenuController extends Controller
{
    /**
     * @Route("/menu", name="designer_menu_index")
     *
     * @return Response
     */
    public function indexAction(): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
        $menus = $menuService->getAll();

        return $this->render('@Designer/menu/index.html.twig', [
            'menus' => $menus
        ]);
    }

    /**
     * @Route("/menu/add", name="designer_menu_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        return $this->render('@Designer/menu/add.html.twig');
    }

    /**
     * @Route("/menu/api/{id}", name="designer_menu_save_with_id", methods={"PUT"})
     * @Route("/menu/api/", name="designer_menu_save_without_id", methods={"POST"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function saveMenuAction(int $id = -1, Request $request): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
        if ($id != -1) {
            $menu = $menuService->get($id);
            $menu->getMenuItems()->clear();
            $menuService->save($menu);
        } else {
            $menu = new Menu();
        }
        $postedMenu = json_decode($request->getContent(), true)['_menu'];

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

            $item->setTitle($child['_title']);
            $item->setMenu($menu);
            $item->setParent($parent);
            $item->setRoute($route);
            $item->setPageType($child['_pageType']);
            $item->setChildren($this->prepareMenuChildren($child['_children'], null, $item));

            $items[] = $item;
        }

        return new ArrayCollection($items);
    }

    /**
     * @Route("/menu/{id}", name="designer_menu_details", methods={"GET"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function detailsAction(int $id, Request $request): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
        $menu = $menuService->get($id);

        return $this->render('@Designer/menu/details.html.twig', [
            'menu' => $menu
        ]);
    }

    /**
     * @Route("/menu/{id}/edit", name="designer_menu_edit")
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function editAction(int $id, Request $request): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
        $menu = $menuService->get($id);

        return $this->render('@Designer/menu/edit.html.twig', [
            'menu' => $menu
        ]);
    }

    /**
     * @Route("/menu/{id}", name="designer_menu_delete", methods={"DELETE"})
     *
     * @param int $id
     * @return Response
     */
    public function deleteAction(int $id): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
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
     * @Route("/menu/api/{id}", name="designer_menu_load_menu", methods={"GET"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function loadAction(int $id, Request $request): Response
    {
        $menuService = $this->get('jinya_gallery.services.menu_service');
        try {
            $menu = $menuService->get($id);

            return $this->json($menu);
        } catch (Exception $exception) {
            return $this->json([
                'success' => false,
                'message' => $exception->getMessage()
            ], 500);
        }
    }
}
