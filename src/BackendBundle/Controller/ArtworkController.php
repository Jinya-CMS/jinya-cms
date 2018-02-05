<?php

namespace BackendBundle\Controller;

use BackendBundle\Form\ArtworkType;
use DataBundle\Entity\Artwork;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_key_exists;
use function array_merge;

class ArtworkController extends Controller
{
    /**
     * @Route("/artworks", name="backend_artworks_index")
     *
     * @return Response
     */
    public function indexAction()
    {
        return $this->redirectToRoute('backend_artworks_overview');
    }

    /**
     * @Route("/artworks/overview", name="backend_artworks_overview")
     *
     * @param Request $request
     * @return Response
     */
    public function overviewAction(Request $request): Response
    {

        return $this->render('@Backend/artworks/overview.html.twig', [
            'search' => $request->get('keyword', '')
        ]);
    }

    protected function render($view, array $parameters = array(), Response $response = null)
    {
        $labelService = $this->get('jinya_gallery.services.label_service');
        $labels = $labelService->getAllLabelsWithArtworks();

        $parameters['labels'] = $labels;

        return parent::render($view, $parameters, $response);
    }

    /**
     * @Route("/artworks/get", name="backend_artworks_getAll")
     *
     * @param Request $request
     * @return Response
     */
    public function getArtworks(Request $request): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $labelService = $this->get('jinya_gallery.services.label_service');
        $offset = $request->get('offset', 0);
        $count = PHP_INT_MAX;
        $keyword = $request->get('keyword', '');
        $labelName = $request->get('label', null);

        if ($labelName) {
            $label = $labelService->getLabel($labelName);
        } else {
            $label = null;
        }

        $allData = $artworkService->getAll($offset, $count, $keyword, $label);
        $allCount = $artworkService->countAll($keyword, $label);

        return $this->json([
            'data' => $allData,
            'more' => $allCount > $count + $offset,
            'moreLink' => $this->generateUrl('backend_artworks_getAll', [
                'keyword' => $keyword,
                'count' => $count,
                'offset' => $offset + $count
            ])
        ]);
    }

    /**
     * @Route("/artworks/add", name="backend_artworks_add")
     *
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request): Response
    {
        $allLabels = $this->createMissingLabels($request);

        $form = $this->createForm(ArtworkType::class, null, ['all_labels' => $allLabels]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artworkService = $this->get('jinya_gallery.services.artwork_service');
            $artworkService->saveOrUpdate($data);

            return $this->redirectToRoute('backend_artworks_index');
        }

        return $this->render('@Backend/artworks/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function createMissingLabels(Request $request): array
    {
        $labelService = $this->get('jinya_gallery.services.label_service');
        $allLabels = $labelService->getAllLabels();

        if ($request->isMethod("POST")) {
            $bundle = $request->get('backend_bundle_artwork_type');

            if (array_key_exists('labelsChoice', $bundle)) {
                $selectedLabels = $bundle['labelsChoice'];
                $labels = [];
                foreach ($selectedLabels as $selectedLabel) {
                    $labels[] = ['name' => $selectedLabel];
                }
                $missingLabels = $labelService->createMissingLabels($selectedLabels);
                $allLabels = array_merge($allLabels, $missingLabels);
            }
        }
        return $allLabels;
    }

    /**
     * @Route("/artworks/edit/{id}", name="backend_artworks_edit")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function editAction(Request $request, int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');

        $allLabels = $this->createMissingLabels($request);

        $artwork = $artworkService->get($id);
        $artwork->setLabelsChoice($artwork->getLabels()->toArray());
        $form = $this->createForm(ArtworkType::class, $artwork, ['all_labels' => $allLabels]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $artworkService->saveOrUpdate($data);

            return $this->redirectToRoute('backend_artworks_details', [
                'id' => $data->getId()
            ]);
        }


        return $this->render('@Backend/artworks/edit.html.twig', [
            'form' => $form->createView(),
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/details/{id}", name="backend_artworks_details")
     *
     * @param int $id
     * @return Response
     */
    public function detailsAction(int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $artwork = $artworkService->get($id);
        return $this->render('@Backend/artworks/details.html.twig', [
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/delete/{id}", name="backend_artworks_delete")
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteAction(Request $request, int $id): Response
    {
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        if ($request->isMethod('POST')) {
            $artworkService->delete($id);

            return $this->redirectToRoute('backend_artworks_overview');
        }

        $artwork = $artworkService->get($id);

        return $this->render('@Backend/artworks/delete.html.twig', [
            'artwork' => $artwork
        ]);
    }

    /**
     * @Route("/artworks/history/{id}", name="backend_artworks_history")
     *
     * @param int $id
     * @return Response
     */
    public function historyAction(int $id): Response
    {
        return $this->forward('BackendBundle:History:index', [
            'class' => Artwork::class,
            'id' => $id,
            'resetRoute' => 'backend_artworks_reset',
            'layout' => '@Backend/artworks/artworks_base.html.twig'
        ]);
    }

    /**
     * @Route("/artworks/history/{id}/reset", name="backend_artworks_reset", methods={"POST"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function resetAction(Request $request, int $id): Response
    {
        $origin = $request->get('origin');
        $artworkService = $this->get('jinya_gallery.services.artwork_service');
        $key = $request->get('key');
        $value = $request->get('value');

        $artworkService->updateField($key, $value, $id);

        return $this->redirect($origin);
    }
}
