<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 26.02.2018
 * Time: 22:40
 */

namespace Jinya\Controller\Api\User;

use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Jinya\Entity\User;
use Jinya\Exceptions\MissingFieldsException;
use Jinya\Formatter\User\UserFormatterInterface;
use Jinya\Services\Users\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Exception\ValidatorException;

class UserController extends BaseUserController
{
    /**
     * @Route("/api/user", methods={"GET"}, name="api_user_get_all")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param Request $request
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @return Response
     */
    public function getAllAction(Request $request, UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($request, $userFormatter, $userService) {
            $offset = $request->get('offset', 0);
            $count = $request->get('count', 10);
            $keyword = $request->get('keyword', '');
            $users = $userService->getAll($offset, $count, $keyword);

            $entityCount = $userService->countAll($keyword);
            $entities = [];

            foreach ($users as $user) {
                $userFormatter
                    ->init($user)
                    ->profile()
                    ->enabled()
                    ->id();

                if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                    $userFormatter->roles();
                }

                $entities[] = $userFormatter->format();
            }

            $route = $request->get('_route');
            $parameter = ['offset' => $offset, 'count' => $count, 'keyword' => $keyword];

            return $this->formatListResult($entityCount, $offset, $count, $parameter, $route, $entities);
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}", methods={"GET"}, name="api_user_get")
     * @IsGranted("ROLE_ADMIN")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @return Response
     */
    public function getAction(int $id, UserServiceInterface $userService, UserFormatterInterface $userFormatter): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $userService, $userFormatter) {
            $user = $userService->get($id);

            $userFormatter = $userFormatter
                ->init($user)
                ->profile()
                ->enabled();

            if ($this->isGranted('ROLE_SUPER_ADMIN')) {
                $userFormatter->roles();
            }

            return $userFormatter->format();
        });

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user", methods={"POST"}, name="api_user_post")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function postAction(UserServiceInterface $userService, UserFormatterInterface $userFormatter, TranslatorInterface $translator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($userService, $userFormatter, $translator) {
            $firstname = $this->getValue('firstname');
            $lastname = $this->getValue('lastname');
            $email = $this->getValue('email');
            $password = $this->getValue('password');
            $enabled = $this->getValue('enabled', false);
            $roles = $this->getValue('roles', []);

            $emptyFields = [];

            if (empty($firstname)) {
                $emptyFields['firstname'] = 'api.user.field.firstname.missing';
            }

            if (empty($lastname)) {
                $emptyFields['lastname'] = 'api.user.field.lastname.missing';
            }

            if (empty($email)) {
                $emptyFields['email'] = 'api.user.field.email.missing';
            }

            if (empty($password)) {
                $emptyFields['password'] = 'api.user.field.password.missing';
            }

            if (!empty($emptyFields)) {
                throw new MissingFieldsException($emptyFields);
            }

            $emailValidator = new EmailValidator();
            if (!$emailValidator->isValid($email, new RFCValidation())) {
                throw new ValidatorException($translator->trans('api.user.field.email.invalid', ['email' => $email], 'validators'));
            }

            $user = new User();
            $user->setEnabled($enabled);
            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRoles($roles);
            $user->setPassword($password);

            $user = $userService->saveOrUpdate($user);

            return $userFormatter
                ->init($user)
                ->profile()
                ->enabled()
                ->roles()
                ->id()
                ->format();
        }, Response::HTTP_CREATED);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}", methods={"PUT"}, name="api_user_put")
     * @IsGranted("ROLE_SUPER_ADMIN", attributes="usermanipulation")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @param UserFormatterInterface $userFormatter
     * @param TranslatorInterface $translator
     * @return Response
     */
    public function putAction(int $id, UserServiceInterface $userService, UserFormatterInterface $userFormatter, TranslatorInterface $translator): Response
    {
        list($data, $status) = $this->tryExecute(function () use ($id, $userService, $userFormatter, $translator) {
            $user = $userService->get($id);

            $firstname = $this->getValue('firstname', $user->getFirstname());
            $lastname = $this->getValue('lastname', $user->getLastname());
            $email = $this->getValue('email', $user->getEmail());
            $enabled = $this->getValue('enabled', $user->isEnabled());
            $roles = $this->getValue('roles', $user->getRoles());

            $emptyFields = [];

            if (empty($firstname)) {
                $emptyFields['firstname'] = 'api.user.field.firstname.missing';
            }

            if (empty($lastname)) {
                $emptyFields['lastname'] = 'api.user.field.lastname.missing';
            }

            if (empty($email)) {
                $emptyFields['email'] = 'api.user.field.email.missing';
            }

            if (!empty($emptyFields)) {
                throw new MissingFieldsException($emptyFields);
            }

            $emailValidator = new EmailValidator();
            if (!$emailValidator->isValid($email, new RFCValidation())) {
                throw new ValidatorException($translator->trans('api.user.field.email.invalid', ['email' => $email], 'validators'));
            }

            if (!$this->isCurrentUser($id)) {
                $user->setEnabled($enabled);
            }

            $user->setEmail($email);
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setRoles($roles);

            $user = $userService->saveOrUpdate($user);

            return $userFormatter
                ->init($user)
                ->profile()
                ->enabled()
                ->roles()
                ->format();
        }, Response::HTTP_OK);

        return $this->json($data, $status);
    }

    /**
     * @Route("/api/user/{id}", methods={"DELETE"}, name="api_user_delete")
     * @IsGranted("ROLE_SUPER_ADMIN")
     *
     * @param int $id
     * @param UserServiceInterface $userService
     * @return Response
     */
    public function deleteAction(int $id, UserServiceInterface $userService): Response
    {
        if (!$this->isCurrentUser($id)) {
            list($data, $status) = $this->tryExecute(function () use ($id, $userService) {
                $userService->delete($id);
            }, Response::HTTP_NO_CONTENT);

            return $this->json($data, $status);
        } else {
            return $this->json(['message' => 'Cannot delete own user'], Response::HTTP_FORBIDDEN);
        }
    }
}
