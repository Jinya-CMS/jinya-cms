<?php

namespace Jinya\Controller\Api\Media;

use Jinya\Entity\Media\File;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Media\FileServiceInterface;
use Jinya\Services\Media\MediaServiceInterface;
use Jinya\Services\Media\Upload\FileUploadServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\MimeTypes;
use Symfony\Component\Routing\Annotation\Route;

class FileContentController extends BaseApiController
{
    /** @var string */
    private string $kernelProjectDir;

    /**
     * @param string $kernelProjectDir
     */
    public function setKernelProjectDir(string $kernelProjectDir): void
    {
        $this->kernelProjectDir = $kernelProjectDir;
    }

    /**
     * @Route("/api/media/file/{id}/content", methods={"GET"}, name="api_file_get_content")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @param MediaServiceInterface $mediaService
     * @param MimeTypes $mimeTypes
     * @return Response
     */
    public function getAction(
        int $id,
        FileServiceInterface $fileService,
        MediaServiceInterface $mediaService,
        MimeTypes $mimeTypes
    ): Response {
        /** @var $data File */
        [$data, $status] = $this->tryExecute(static function () use ($fileService, $id) {
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
            $data->getName() . $mimeTypes->getExtensions($data->getType())[0]
        );
    }

    /**
     * @Route("/api/media/file/{id}/content", methods={"POST"}, name="api_file_post_content_upload_start")
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
     * @Route(
     *     "/api/media/file/{id}/content/{position}",
     *     methods={"PUT"},
     *     requirements={"position": "^\d*$"},
     *     name="api_file_post_content_upload_chunk"
     * )
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
     * @Route("/api/media/file/{id}/content/state", methods={"DELETE"}, name="api_file_post_content_upload_state_clear")
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
     * @Route("/api/media/file/{id}/content/finish", methods={"PUT"}, name="api_file_post_content_upload_finish")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param FileServiceInterface $fileService
     * @param FileUploadServiceInterface $fileUploadService
     * @param MimeTypes $mimeTypes
     * @return Response
     */
    public function finishUploadAction(
        int $id,
        FileServiceInterface $fileService,
        FileUploadServiceInterface $fileUploadService,
        MimeTypes $mimeTypes
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($mimeTypes, $id, $fileService, $fileUploadService) {
            $file = $fileService->get($id);
            $path = $fileUploadService->finishUpload($id);

            $mimeType = $mimeTypes->guessMimeType($this->kernelProjectDir . '/public' . $path);

            $file->setPath($path);
            $file->setType($mimeType ?? 'application/octet-stream');

            $fileService->saveOrUpdate($file);

            $fileUploadService->cleanupAfterUpload($id);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
