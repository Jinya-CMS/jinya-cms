<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 18.02.2018
 * Time: 15:29
 */

namespace Jinya\Controller\Api\Label;

use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Label\LabelFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Labels\LabelServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LabelController extends BaseApiController
{
    /**
     * @Route("/api/label", methods={"GET"}, name="api_label_get_all")
     * @IsGranted("ROLE_WRITER")
     *
     * @param LabelServiceInterface $labelService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function getAllAction(LabelServiceInterface $labelService, LabelFormatterInterface $labelFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($labelService, $labelFormatter) {
            $labels = $labelService->getAllLabels();
            $result = [];

            foreach ($labels as $label) {
                $result[] = $labelFormatter
                    ->init($label)
                    ->name()
                    ->format();
            }

            return $result;
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/label/{name}", methods={"GET"}, name="api_label_get")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $name
     * @param LabelServiceInterface $labelService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function getAction(string $name, LabelServiceInterface $labelService, LabelFormatterInterface $labelFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $labelService, $labelFormatter) {
            $label = $labelService->getLabel($name);

            return $labelFormatter
                ->init($label)
                ->name()
                ->artworks()
                ->galleries()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/label", methods={"POST"}, name="api_label_post")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param LabelServiceInterface $labelService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function postAction(Request $request, LabelServiceInterface $labelService, LabelFormatterInterface $labelFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $labelService, $labelFormatter) {
            $name = $this->getValue('name', null);
            if (empty($name)) {
                throw new MissingFieldsException(['name' => 'api.label.field.name.missing']);
            }

            return $labelFormatter->init($labelService->addLabel($name))->name()->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/label/{name}", methods={"PUT"}, name="api_label_put")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $name
     * @param Request $request
     * @param LabelServiceInterface $labelService
     * @param LabelFormatterInterface $labelFormatter
     * @return Response
     */
    public function putAction(string $name, Request $request, LabelServiceInterface $labelService, LabelFormatterInterface $labelFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $request, $labelService, $labelFormatter) {
            $newName = $this->getValue('name', $name);

            return $labelFormatter
                ->init($labelService->rename($name, $newName))
                ->name()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/label/{name}", methods={"DELETE"}, name="api_label_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $name
     * @param LabelServiceInterface $labelService
     * @return Response
     */
    public function deleteAction(string $name, LabelServiceInterface $labelService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($name, $labelService) {
            $labelService->deleteLabel($name);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}