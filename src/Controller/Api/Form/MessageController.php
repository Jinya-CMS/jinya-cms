<?php

namespace Jinya\Controller\Api\Form;

use Jinya\Entity\Form\Message;
use Jinya\Formatter\Form\MessageFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\MessageServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends BaseApiController
{
    /**
     * @Route("/api/message", methods={"GET"}, name="api_message_get_all")
     * @Route("/api/message/{action}", methods={"GET"}, name="api_message_get_all_by_action")
     * @Route("/api/{formSlug}/message", methods={"GET"}, name="api_message_get_all_by_form")
     *
     * @param Request $request
     * @param MessageServiceInterface $messageService
     * @param MessageFormatterInterface $messageFormatter
     * @param string $formSlug
     * @param string $action
     * @return Response
     */
    public function getAllAction(
        Request $request,
        MessageServiceInterface $messageService,
        MessageFormatterInterface $messageFormatter,
        string $formSlug = '',
        string $action = 'all'
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use (
            $action,
            $messageFormatter,
            $messageService,
            $request,
            $formSlug
        ) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entityCount = $messageService->countAll($keyword, $formSlug, $action);
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
                    ->id()
                    ->read()
                    ->subject()
                    ->format();
            }, $messageService->getAll($offset, $count, $keyword, $formSlug, $action));

            $parameter = [
                'offset' => $offset,
                'count' => $count,
                'keyword' => $keyword,
                'formSlug' => $formSlug,
                'action' => $action
            ];

            if ($formSlug !== '') {
                return $this->formatListResult(
                    $entityCount,
                    $offset,
                    $count,
                    $parameter,
                    'api_message_get_all_by_form',
                    $entities
                );
            }

            if ($action !== '') {
                return $this->formatListResult(
                    $entityCount,
                    $offset,
                    $count,
                    $parameter,
                    'api_message_get_all_by_action',
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
                ->archived()
                ->trash()
                ->id()
                ->read()
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
    public function putSpamAction(int $id, MessageServiceInterface $messageService): Response
    {
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
     * @Route("/api/message/{id}/inbox", methods={"PUT"}, name="api_message_put_inbox")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putInboxAction(int $id, MessageServiceInterface $messageService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $message = $messageService->get($id);
            $message->setIsArchived(false);
            $message->setSpam(false);
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
    public function putArchiveAction(int $id, MessageServiceInterface $messageService): Response
    {
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
     * @Route("/api/message/{id}/trash", methods={"PUT"}, name="api_message_put_trash")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putTrashAction(int $id, MessageServiceInterface $messageService): Response
    {
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
     * @Route("/api/message/{id}/read", methods={"PUT"}, name="api_message_put_read")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function putReadAction(int $id, MessageServiceInterface $messageService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $message = $messageService->get($id);
            $message->setIsRead(true);

            $messageService->saveOrUpdate($message);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/message/{id}", methods={"DELETE"}, name="api_message_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param int $id
     * @param MessageServiceInterface $messageService
     * @return Response
     */
    public function deleteAction(int $id, MessageServiceInterface $messageService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($id, $messageService) {
            $messageService->delete($messageService->get($id));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
