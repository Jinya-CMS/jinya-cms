<?php

namespace Jinya\Controller\Api\Form;

use Jinya\Entity\Form\Message;
use Jinya\Formatter\Form\MessageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\FormServiceInterface;
use Jinya\Services\Form\MessageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends BaseApiController
{
    /**
     * @Route("/api/message", methods={"GET"}, name="api_message_get_all")
     * @Route("/api/{formId}/message", methods={"GET"}, name="api_message_get_all_by_form")
     *
     * @param Request $request
     * @param MessageServiceInterface $messageService
     * @param MessageFormatterInterface $messageFormatter
     * @param int $formId
     * @return Response
     */
    public function getAllAction(
        Request $request,
        MessageServiceInterface $messageService,
        MessageFormatterInterface $messageFormatter,
        int $formId = -1
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use (
            $messageFormatter,
            $messageService,
            $request,
            $formId
        ) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entityCount = $messageService->countAll($keyword, $formId);
            $entities = array_map(static function (Message $message) use ($messageFormatter) {
                return $messageFormatter
                    ->init($message)
                    ->form()
                    ->fromAddress()
                    ->toAddress()
                    ->content()
                    ->sendAt()
                    ->spam()
                    ->archived()
                    ->trash()
                    ->subject()
                    ->format();
            }, $messageService->getAll($offset, $count, $keyword, $formId));

            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword, 'formId' => $formId];

            if ($formId !== -1) {
                return $this->formatListResult(
                    $entityCount,
                    $offset,
                    $count,
                    $parameter,
                    'api_message_get_all_by_form',
                    $entities
                );
            }
            return $this->formatListResult(
                $entityCount,
                $offset,
                $count,
                $parameter,
                'api_message_get_all',
                $entities
            );
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/message/{id}", methods={"GET"}, name="api_message_get")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @param MessageFormatterInterface $messageFormatter
     * @return Response
     */
    public function getAction(
        int $id,
        MessageServiceInterface $messageService,
        MessageFormatterInterface $messageFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService, $messageFormatter) {
            $message = $messageService->get($id);
            return $messageFormatter
                ->init($message)
                ->form()
                ->fromAddress()
                ->toAddress()
                ->content()
                ->sendAt()
                ->spam()
                ->subject()
                ->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/message/{id}/spam", methods={"PUT"}, name="api_message_put_spam")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putSpamAction(
        int $id,
        MessageServiceInterface $messageService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $message = $messageService->get($id);
            $message->setIsArchived(false);
            $message->setSpam(true);
            $message->setIsDeleted(false);

            $messageService->saveOrUpdate($message);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/message/{id}/archive", methods={"PUT"}, name="api_message_put_archive")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putArchiveAction(
        int $id,
        MessageServiceInterface $messageService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $message = $messageService->get($id);
            $message->setIsArchived(true);
            $message->setSpam(false);
            $message->setIsDeleted(false);

            $messageService->saveOrUpdate($message);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/message/{slug}/trash", methods={"PUT"}, name="api_message_put_trash")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putTrashAction(
        int $id,
        MessageServiceInterface $messageService
    ): Response {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $message = $messageService->get($id);
            $message->setIsArchived(false);
            $message->setSpam(false);
            $message->setIsDeleted(true);

            $messageService->saveOrUpdate($message);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/message/{id}", methods={"DELETE"}, name="api_message_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function deleteAction(
        string $slug,
        FormServiceInterface $formService
    ): Response {
        list($data, $status) = $this->tryExecute(function () use ($slug, $formService) {
            $formService->delete($formService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
