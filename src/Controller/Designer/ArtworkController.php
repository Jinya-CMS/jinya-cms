<?php

namespace Jinya\Controller\Designer;

use Jinya\Framework\BaseController;
use Jinya\Services\Artworks\ArtworkServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArtworkController extends BaseController
{
    /**
     * @Route("/designer/artwork/api", name="designer_artwork_list_all", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function apiListAllAction(Request $request, ArtworkServiceInterface $artworkService): Response
    {
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
