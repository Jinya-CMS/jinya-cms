<?php

namespace DesignerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtworkController extends Controller
{
    /**
     * @Route("/artwork/api", name="designer_artwork_list_all", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function apiListAllAction(Request $request): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $offset = $request->get('offset', 0);
        $count = $request->get('count', 12);
        $keyword = $request->get('keyword', '');
        $allData = $artworkService->getAll($offset, $count, $keyword);
        $allCount = $artworkService->countAll($keyword);

        return $this->json([
            'data' => $allData,
            'more' => $allCount > $count + $offset,
            'moreLink' => $this->generateUrl('designer_artwork_list_all', [
                'keyword' => $keyword,
                'count' => $count,
                'offset' => $offset + $count
            ])
        ]);
    }
}
