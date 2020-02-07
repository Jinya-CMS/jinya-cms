<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 01.03.2018
 * Time: 07:54
 */

namespace Jinya\Controller\Api\Form;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Jinya\Entity\Form\Form;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\Form\FormFormatterInterface;
use Jinya\Framework\BaseApiController;
use Jinya\Services\Form\FormServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Contracts\Translation\TranslatorInterface;

class FormController extends BaseApiController
{
    /**
     * @Route("/api/form", methods={"GET"}, name="api_form_get_all")
     *
     * @param Request $request
     * @param FormServiceInterface $formService
     * @param FormFormatterInterface $formFormatter
     * @return Response
     */
    public function getAllAction(
        Request $request,
        FormServiceInterface $formService,
        FormFormatterInterface $formFormatter
    ): Response {
        [$data, $statusCode] = $this->tryExecute(function () use ($formFormatter, $formService, $request) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');

            $entityCount = $formService->countAll($keyword);
            $entities = array_map(static function ($form) use ($formFormatter) {
                return $formFormatter
                    ->init($form)
                    ->slug()
                    ->title()
                    ->description()
                    ->items()
                    ->format();
            }, $formService->getAll($keyword));

            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, 'api_form_get_all', $entities);
        });

        return $this->json($data, $statusCode);
    }

    /**
     * @Route("/api/form/{slug}", methods={"GET"}, name="api_form_get")
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @param FormFormatterInterface $formFormatter
     * @return Response
     */
    public function getAction(
        string $slug,
        FormServiceInterface $formService,
        FormFormatterInterface $formFormatter
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $formService, $formFormatter) {
            $form = $formService->get($slug);
            $formFormatter
                ->init($form)
                ->slug()
                ->description()
                ->title()
                ->items();

            if ($this->isGranted('ROLE_WRITER')) {
                $formFormatter
                    ->created()
                    ->updated()
                    ->history()
                    ->name()
                    ->emailTemplate()
                    ->toAddress();
            }

            return $formFormatter->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form", methods={"POST"}, name="api_form_post")
     * @IsGranted("ROLE_WRITER")
     *
     * @param FormServiceInterface $formService
     * @param FormFormatterInterface $formFormatter
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function postAction(
        FormServiceInterface $formService,
        FormFormatterInterface $formFormatter,
        TranslatorInterface $translator
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($formService, $formFormatter, $translator) {
            $title = $this->getValue('title');
            $slug = $this->getValue('slug', '');
            $description = $this->getValue('description', '');
            $name = $this->getValue('name', $title);
            $toAddress = $this->getValue('toAddress');

            $missingFields = [];
            if (empty($title)) {
                $missingFields['title'] = 'api.form.field.title.empty';
            }
            if (empty($toAddress)) {
                $missingFields['toAddress'] = 'api.form.field.toAddress.empty';
            }
            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $emailValidator = new EmailValidator();
            if (!$emailValidator->isValid($toAddress, new RFCValidation())) {
                throw new ValidatorException($translator->trans('api.form.field.toAddress.invalid', ['toAddress' => $toAddress], 'validators'));
            }

            $form = new Form();
            $form->setName($name);
            $form->setDescription($description);
            $form->setTitle($title);
            $form->setToAddress($toAddress);
            $form->setSlug($slug);

            $formService->saveOrUpdate($form);

            return $formFormatter
                ->init($form)
                ->slug()
                ->title()
                ->name()
                ->description()
                ->toAddress()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}", methods={"PUT"}, name="api_form_put")
     * @IsGranted("ROLE_WRITER")
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function putAction(
        string $slug,
        FormServiceInterface $formService,
        TranslatorInterface $translator
    ): Response {
        [$data, $status] = $this->tryExecute(function () use ($slug, $formService, $translator) {
            $form = $formService->get($slug);

            $slug = $this->getValue('slug', $form->getSlug());
            $title = $this->getValue('title', $form->getTitle());
            $name = $this->getValue('name', $form->getName());
            $description = $this->getValue('description', $form->getDescription());
            $toAddress = $this->getValue('toAddress', $form->getToAddress());

            $missingFields = [];
            if (empty($title)) {
                $missingFields['title'] = 'api.form.field.title.empty';
            }
            if (empty($toAddress)) {
                $missingFields['toAddress'] = 'api.form.field.toAddress.empty';
            }
            if (!empty($missingFields)) {
                throw new MissingFieldsException($missingFields);
            }

            $emailValidator = new EmailValidator();
            if (!$emailValidator->isValid($toAddress, new RFCValidation())) {
                throw new ValidatorException($translator->trans('api.form.field.toAddress.invalid', ['toAddress' => $toAddress], 'validators'));
            }

            $form->setName($name);
            $form->setDescription($description);
            $form->setTitle($title);
            $form->setSlug($slug);

            if ($this->isGranted('ROLE_ADMIN')) {
                $form->setToAddress($toAddress);
            }

            $formService->saveOrUpdate($form);
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/form/{slug}", methods={"DELETE"}, name="api_form_delete")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param string $slug
     * @param FormServiceInterface $formService
     * @return Response
     */
    public function deleteAction(string $slug, FormServiceInterface $formService): Response
    {
        [$data, $status] = $this->tryExecute(static function () use ($slug, $formService) {
            $formService->delete($formService->get($slug));
        }, Response::HTTP_NO_CONTENT);

        return $this->json($data, $status);
    }
}
