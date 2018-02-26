<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 22:40
 */

namespace Jinya\Controller\Api\User;


use Jinya\Formatter\User\UserFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends BaseApiController
{
    /**
     * @Route("/api/user", methods={"GET"}, name="api_user_get_all")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @return Response
     */
    public function getAllAction(Request $request, UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $userFormatter, $userService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $users = $userService->getAll($offset, $count, $keyword);

            $entityCount = $userService->countAll($keyword);
            $entities = [];

            foreach ($users as $user) {
                $entities[] = $userFormatter
                    ->init($user)
                    ->profile()
                    ->enabled()
                    ->format();
            }

            $route = $request->get('_route');
            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, $route, $entities);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{email}", methods={"GET"}, name="api_user_get")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @return Response
     */
    public function getAction(int $id, UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $userService, $userFormatter) {
            $user = $userService->get($id);

            return $userFormatter
                ->init($user)
                ->profile()
                ->enabled()
                ->roles();
        });

        return $this->json($data, $status);
    }
}