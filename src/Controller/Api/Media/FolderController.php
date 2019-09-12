<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Entity\Media\Folder;
use Jinya\Formatter\Media\FolderFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\FolderServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FolderController extends BaseApiController
{
    /**
     * @Route("/api/media/folder", methods={"GET"}, name="api_folder_get_all")
     *
     * @param Request $request
     * @param FolderServiceInterface $folderService
     * @param FolderFormatterInterface $folderFormatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        FolderServiceInterface $folderService,
        FolderFormatterInterface $folderFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(static function () use (
            $folderService,
            $request,
            $folderFormatter,
            $folderService
        ) {
            $keyword = $request->get('keyword', '');
            $folderId = $request->get('folder', -1);
            $tag = $request->get('tag', '');
            $folder = null;

            if ($folderId !== -1) {
                $folder = $folderService->get($folderId);
            }

            $entityCount = $folderService->countAll($keyword, $folder, $tag);
            $entities = array_map(static function ($folder) use ($folderFormatter) {
                return $folderFormatter
                    ->init($folder)
                    ->name()
                    ->id()
                    ->tags()
                    ->parent()
                    ->format();
            }, $folderService->getAll($keyword, $folder, $tag));

            return ['items' => $entities, 'count' => $entityCount];
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/media/folder/{id}", methods={"GET"}, name="api_folder_get")
     *
     * @param int $id
     * @param FolderServiceInterface $folderService
     * @param FolderFormatterInterface $folderFormatter
     * @return Response
     */
    public function getAction(
        int $id,
        FolderServiceInterface $folderService,
        FolderFormatterInterface $folderFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($id, $folderFormatter, $folderService) {
            $folder = $folderService->get($id);
            $result = $folderFormatter->init($folder)
                ->name()
                ->id()
                ->tags()
                ->parent()
                ->files()
                ->folders();

            if ($this->isGranted('ROLE_WRITER')) {
                $result = $result->updated()
                    ->created();
            }

            return $result->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/folder", methods={"POST"}, name="api_folder_post")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param FolderServiceInterface $folderService
     * @return Response
     */
    public function postAction(FolderServiceInterface $folderService): Response
    {
        [$data, $status] = $this->tryExecute(function () use ($folderService) {
            $name = $this->getValue('name');
            $folderId = $this->getValue('folder');

            $parentFolder = $folderService->get($folderId);
            $folder = new Folder();
            $folder->setName($name);
            $folder->setParent($parentFolder);

            $folderService->saveOrUpdate($folder);
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/folder/{id}", methods={"PUT"}, name="api_folder_put")
     * @IsGranted("ROLE_WRITER", statusCode=403)
     *
     * @param int $id
     * @param FolderServiceInterface $folderService
     * @return Response
     */
    public function putAction(
        int $id,
        FolderServiceInterface $folderService
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($id, $folderService) {
            $folder = $folderService->get($id);
            $name = $this->getValue('name', $folder->getName());
            $folderId = $this->getValue('folder', $folder->getParent()->getId());

            $folder = $folderService->get($folderId);
            $folder->setName($name);
            $folder->setParent($folder);

            $folderService->saveOrUpdate($folder);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/folder/{id}", methods={"DELETE"}, name="api_folder_delete")
     * @IsGranted("ROLE_ADMIN", statusCode=403)
     *
     * @param int $id
     * @param FolderServiceInterface $folderService
     * @return Response
     */
    public function deleteAction(int $id, FolderServiceInterface $folderService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $folderService) {
            $folder = $folderService->get($id);
            $folderService->delete($folder);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
