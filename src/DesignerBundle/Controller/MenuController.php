<?php

namespace DesignerBundle\Controller;

use DataBundle\Entity\Menu;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
     * @param Request $request
     * @return Response
     */
    public function saveMenuAction(Request $request): Response
    {
        $router = $this->get('router');
        $menu = new Menu();
        $menuService = $this->get('jinya_gallery.services.menu_service');
        $menu = $menuService->save($menu);

        return $this->json([
            'success' => true,
            'redirectTarget' => $router->generate('designer_menu_details', ['id' => $menu->getId()])
        ]);
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
