<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 02.03.2018
 * Time: 07:53
 */

namespace Jinya\Controller\Api\Form;


use Jinya\Entity\FormItem;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Form\FormItemFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\FormItemServiceInterface;
use Jinya\Services\Form\FormServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function array_key_exists;
use function array_map;

class FormItemController extends BaseApiController
{
    /**
     * @Route("/api/form/{slug}/items", methods={"GET"}, name="api_form_item_get_all")
     *
     * @param string $slug
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function getAllAction(string $slug, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $formItemService, $formItemFormatter) {
            return array_map(function ($item) use ($formItemFormatter) {
                return $formItemFormatter
                    ->init($item)
                    ->form()
                    ->options()
                    ->label()
                    ->position()
                    ->type()
                    ->format();
            }, $formItemService->getItems($slug));
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}/items/{position}", methods={"GET"}, name="api_form_item_get")
     *
     * @param string $slug
     * @param int $position
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function getAction(string $slug, int $position, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $position, $formItemService, $formItemFormatter) {
            $formItemFormatter
                ->init($formItemService->getItem($slug, $position))
                ->form()
                ->position()
                ->label()
                ->helpText()
                ->options()
                ->type();

            if ($this->isGranted('ROLE_WRITER')) {
                $formItemFormatter->created()
                    ->updated()
                    ->history();
            }

            return $formItemFormatter->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}/items/{position}", methods={"POST"}, name="api_form_item_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param int $position
     * @param FormServiceInterface $formService
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function postAction(string $slug, int $position, FormServiceInterface $formService, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $position, $formService, $formItemService, $formItemFormatter) {
            $form = $formService->get($slug);
            $label = $this->getValue('label');
            $helpText = $this->getValue('helpText', '');
            $options = $this->getValue('options', []);
            $type = $this->getValue('type', TextType::class);

            if (empty($label)) {
                throw new MissingFieldsException(['label' => 'api.form.items.field.label.missing']);
            }

            if (empty($options) || !array_key_exists('required', $options)) {
                $options['required'] = false;
            }

            $item = new FormItem();
            $item->setPosition($position);
            $item->setType($type);
            $item->setOptions($options);
            $item->setLabel($label);
            $item->setHelpText($helpText);
            $item->setForm($form);

            $formItemService->addItem($item);

            return $formItemFormatter
                ->init($item)
                ->helpText()
                ->label()
                ->options()
                ->type()
                ->position()
                ->form()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}/items/{position}", methods={"PUT"}, name="api_form_item_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param int $position
     * @param FormServiceInterface $formService
     * @param FormItemServiceInterface $formItemService
     * @param FormItemFormatterInterface $formItemFormatter
     * @return Response
     */
    public function putAction(string $slug, int $position, FormServiceInterface $formService, FormItemServiceInterface $formItemService, FormItemFormatterInterface $formItemFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $position, $formService, $formItemService, $formItemFormatter) {
            $item = $formItemService->getItem($slug, $position);
            $label = $this->getValue('label', $item->getLabel());
            $helpText = $this->getValue('helpText', $item->getHelpText());
            $options = $this->getValue('options', $item->getOptions());
            $type = $this->getValue('type', $item->getType());

            if (empty($options) || !array_key_exists('required', $options)) {
                $options['required'] = false;
            }

            $item->setPosition($position);
            $item->setType($type);
            $item->setOptions($options);
            $item->setLabel($label);
            $item->setHelpText($helpText);

            $formItemService->updateItem($item);

            return $formItemFormatter
                ->init($item)
                ->helpText()
                ->label()
                ->options()
                ->type()
                ->position()
                ->form()
                ->format();
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}/items/{position}", methods={"DELETE"}, name="api_form_item_delete")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param int $position
     * @param FormServiceInterface $formService
     * @param FormItemServiceInterface $formItemService
     * @return Response
     */
    public function deleteAction(string $slug, int $position, FormServiceInterface $formService, FormItemServiceInterface $formItemService): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($slug, $position, $formService, $formItemService) {
            $formItemService->deleteItem($formService->get($slug), $position);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}