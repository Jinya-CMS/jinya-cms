<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Entity\Media\File;
use Jinya\Formatter\Media\FileFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\FileServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileController extends BaseApiController
{
    /**
     * @Route("/api/media/file", methods={"GET"}, name="api_file_get_all")
     *
     * @param Request $request
     * @param FileServiceInterface $fileService
     * @param FileFormatterInterface $fileFormatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        FileServiceInterface $fileService,
        FileFormatterInterface $fileFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use (
            $request,
            $fileFormatter,
            $fileService
        ) {
            $keyword = $request->get('keyword', '');
            $tag = $request->get('tag', '');
            $type = $request->get('type', '');

            $entityCount = $fileService->countAll($keyword, $tag, $type);
            $entities = array_map(function ($file) use ($fileFormatter) {
                $result = $fileFormatter
                    ->init($file)
                    ->name()
                    ->id()
                    ->tags()
                    ->galleries()
                    ->type()
                    ->path();

                if ($this->isGranted('ROLE_WRITER')) {
                    $result = $result
                        ->updated()
                        ->created();
                }

                return $result->format();
            }, $fileService->getAll($keyword, $tag, $type));

            return ['items' => $entities, 'count' => $entityCount];
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/media/file/{id}", methods={"GET"}, name="api_file_get")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @param FileFormatterInterface $fileFormatter
     * @return Response
     */
    public function getAction(
        int $id,
        FileServiceInterface $fileService,
        FileFormatterInterface $fileFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($id, $fileFormatter, $fileService) {
            $file = $fileService->get($id);
            $result = $fileFormatter->init($file)
                ->name()
                ->id()
                ->tags()
                ->galleries()
                ->type()
                ->path();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result
                    ->updated()
                    ->created();
            }

            return $result->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file", methods={"POST"}, name="api_file_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param FileServiceInterface $fileService
     * @param FileFormatterInterface $fileFormatter
     * @return Response
     */
    public function postAction(FileServiceInterface $fileService, FileFormatterInterface $fileFormatter): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($fileFormatter, $fileService) {
            $name = $this->getValue('name');

            $file = new File();
            $file->setName($name);

            $fileService->saveOrUpdate($file);
            $result = $fileFormatter->init($file)
                ->name()
                ->id()
                ->tags()
                ->galleries()
                ->type()
                ->path();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result
                    ->updated()
                    ->created();
            }

            return $result->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}", methods={"PUT"}, name="api_file_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @return Response
     */
    public function putAction(int $id, FileServiceInterface $fileService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($id, $fileService) {
            $file = $fileService->get($id);
            $name = $this->getValue('name', $file->getName());

            $file->setName($name);

            $fileService->saveOrUpdate($file);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}", methods={"DELETE"}, name="api_file_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @return Response
     */
    public function deleteAction(int $id, FileServiceInterface $fileService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $fileService) {
            $file = $fileService->get($id);
            $fileService->delete($file);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
