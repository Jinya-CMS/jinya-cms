<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Entity\Media\File;
use Jinya\Formatter\Media\FileFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\FolderServiceInterface;
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
     * @param FolderServiceInterface $folderService
     * @param FileFormatterInterface $fileFormatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        FileServiceInterface $fileService,
        FolderServiceInterface $folderService,
        FileFormatterInterface $fileFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(static function () use (
            $folderService,
            $request,
            $fileFormatter,
            $fileService
        ) {
            $keyword = $request->get('keyword', '');
            $folderId = $request->get('folder', -1);
            $tag = $request->get('tag', '');
            $folder = null;

            if ($folderId !== -1) {
                $folder = $folderService->get($folderId);
            }

            $entityCount = $fileService->countAll($keyword, $folder, $tag);
            $entities = array_map(static function ($file) use ($fileFormatter) {
                return $fileFormatter
                    ->init($file)
                    ->name()
                    ->type()
                    ->id()
                    ->tags()
                    ->galleries()
                    ->format();
            }, $fileService->getAll($keyword, $folder, $tag));

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
                ->folder()
                ->path();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->created();
            }

            return $result->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file", methods={"POST"}, name="api_file_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param FileServiceInterface $fileService
     * @param FolderServiceInterface $folderService
     * @return Response
     */
    public function postAction(FileServiceInterface $fileService, FolderServiceInterface $folderService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($fileService, $folderService) {
            $name = $this->getValue('name');
            $folderId = $this->getValue('folder');

            $folder = $folderService->get($folderId);
            $file = new File();
            $file->setName($name);
            $file->setFolder($folder);

            $fileService->saveOrUpdate($file);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}", methods={"PUT"}, name="api_file_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @param FolderServiceInterface $folderService
     * @return Response
     */
    public function putAction(
        int $id,
        FileServiceInterface $fileService,
        FolderServiceInterface $folderService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($id, $fileService, $folderService) {
            $file = $fileService->get($id);
            $name = $this->getValue('name', $file->getName());
            $folderId = $this->getValue('folder', $file->getFolder()->getId());

            $folder = $folderService->get($folderId);
            $file->setName($name);
            $file->setFolder($folder);

            $fileService->saveOrUpdate($file);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}", methods={"DELETE"}, name="api_file_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
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
