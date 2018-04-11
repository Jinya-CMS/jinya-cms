<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 04.03.2018
 * Time: 17:27
 */

namespace Jinya\Controller\Api\Menu;


use Jinya\Entity\Menu;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Menu\MenuFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Menu\MenuServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_map;
use function count;

class MenuController extends BaseApiController
{
    /**
     * @Route("/api/menu", methods={"GET"}, name="api_menu_get_all")
     *
     * @param MenuServiceInterface $menuService
     * @param MenuFormatterInterface $menuFormatter
     * @return Response
     */
    public function getAllAction(MenuServiceInterface $menuService, MenuFormatterInterface $menuFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($menuService, $menuFormatter) {
            $items = array_map(function ($menu) use ($menuFormatter) {
                return $menuFormatter
                    ->init($menu)
                    ->name()
                    ->logo()
                    ->id()
                    ->format();
            }, $menuService->getAll());

            return $this->formatListResult(count($items), 0, count($items), [], 'api_menu_get_all', $items);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}", methods={"GET"}, name="api_menu_get")
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @param MenuFormatterInterface $menuFormatter
     * @return Response
     */
    public function getAction(int $id, MenuServiceInterface $menuService, MenuFormatterInterface $menuFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $menuService, $menuFormatter) {
            return $menuFormatter->init($menuService->get($id))
                ->id()
                ->logo()
                ->name()
                ->items()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu", methods={"POST"}, name="api_menu_post")
     *
     * @param MenuServiceInterface $menuService
     * @param MenuFormatterInterface $menuFormatter
     * @return Response
     */
    public function postAction(MenuServiceInterface $menuService, MenuFormatterInterface $menuFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($menuService, $menuFormatter) {
            $name = $this->getValue('name');

            if (empty($name)) {
                throw new MissingFieldsException(['name' => 'api.menu.field.missing']);
            }

            $menu = new Menu();
            $menu->setName($name);
            $menu->setLogo('');

            $menuService->save($menu);

            return $menuFormatter
                ->init($menu)
                ->id()
                ->name()
                ->logo()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}", methods={"PUT"}, name="api_menu_put")
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @return Response
     */
    public function putAction(int $id, MenuServiceInterface $menuService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $menuService) {
            $menu = $menuService->get($id);

            $name = $this->getValue('name', $menu->getName());
            $menu->setName($name);

            $menuService->save($menu);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/menu/{id}", methods={"DELETE"}, name="api_menu_delete")
     *
     * @param int $id
     * @param MenuServiceInterface $menuService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function deleteAction(int $id, MenuServiceInterface $menuService, MediaServiceInterface $mediaService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $menuService, $mediaService) {
            $menu = $menuService->get($id);
            if (!empty($menu->getLogo())) {
                $mediaService->deleteMedia($menu->getLogo());
            }

            $menuService->delete($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}