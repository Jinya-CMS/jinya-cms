<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Entity\Media\File;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Media\Upload\FileUploadServiceInterface;
use Mimey\MimeTypes;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FileContentController extends BaseApiController
{
    /**
     * @Route("/api/media/file/{id}/content", name="api_file_get", methods={"GET"})
     *
     * @param int $id
     * @param MimeTypes $mimeTypes
     * @param FileServiceInterface $fileService
     * @param MediaServiceInterface $mediaService
     * @return Response
     */
    public function getAction(
        int $id,
        MimeTypes $mimeTypes,
        FileServiceInterface $fileService,
        MediaServiceInterface $mediaService
    ): Response {
        /** @var $data File */
        [$data, $status] = $this->tryExecute(static function () use ($fileService, $id, $mimeTypes) {
            $file = $fileService->get($id);
            if (empty($file->getPath())) {
                throw new FileNotFoundException($file->getName());
            }

            return $file;
        });

        if (200 !== $status) {
            return $this->json($data, $status);
        }

        return $this->file(
            $mediaService->getMedia($data->getPath()),
            $data->getName() . $mimeTypes->getExtension($data->getType())
        );
    }

    /**
     * @Route("/api/media/file/{id}/content", methods={"POST"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param FileUploadServiceInterface $fileUploadService
     * @return Response
     */
    public function startUploadAction(int $id, FileUploadServiceInterface $fileUploadService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $fileUploadService) {
            $fileUploadService->startUpload($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}/content/{position}", methods={"PUT"}, requirements={"position": "^\d*$"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param int $position
     * @param Request $request
     * @param FileUploadServiceInterface $fileUploadService
     * @return Response
     */
    public function uploadChunkAction(
        int $id,
        int $position,
        Request $request,
        FileUploadServiceInterface $fileUploadService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $position, $request, $fileUploadService) {
            $data = $request->getContent(true);

            $fileUploadService->uploadChunk($data, $position, $id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}/content/state", methods={"DELETE"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param FileUploadServiceInterface $fileUploadService
     * @return Response
     */
    public function resetStateAction(int $id, FileUploadServiceInterface $fileUploadService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $fileUploadService) {
            $fileUploadService->cleanupAfterUpload($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/media/file/{id}/content/finish", methods={"PUT"})
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @param FileUploadServiceInterface $fileUploadService
     * @return Response
     */
    public function finishUploadAction(
        int $id,
        FileServiceInterface $fileService,
        FileUploadServiceInterface $fileUploadService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $fileService, $fileUploadService) {
            $file = $fileService->get($id);
            $path = $fileUploadService->finishUpload($id);

            $file->setPath($path);
            $file->setType($path);

            $fileService->saveOrUpdate($file);

            $fileUploadService->cleanupAfterUpload($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
