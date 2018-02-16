<?php

namespace Jinya\Controller\Backend;

use Doctrine\ORM\EntityManagerInterface;
use Jinya\Entity\HistoryEnabledEntity;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends Controller
{
    /**
     * @Route("/history/{class}/{id}/{resetRoute}?layout={layout}", name="backend_history_index")
     *
     * @param int $id
     * @param string $class
     * @param string $resetRoute
     * @param string $layout
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function indexAction(int $id, string $class, string $resetRoute, string $layout, EntityManagerInterface $entityManager): Response
    {
        /** @var HistoryEnabledEntity $entity */
        $entity = $entityManager->find($class, $id);
        $history = $entity->getHistory();

        foreach ($history as $key => $item) {
            $date = new DateTime($item['timestamp']);
            $item['timestamp'] = $date;
            $history[$key] = $item;
        }

        return $this->render('@Backend/history/index.html.twig', [
            'history' => $history,
            'reset_route' => $resetRoute,
            'base_layout' => $layout,
            'id' => $id
        ]);
    }
}
