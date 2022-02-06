<?php

namespace App\Web\Actions\Frontend;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\MenuItem;
use App\Messaging\FormMessageHandler;
use App\Web\Exceptions\MissingFieldsException;
use Jinya\PDOx\Exceptions\InvalidQueryException;
use Jinya\PDOx\Exceptions\NoResultException;
use PHPMailer\PHPMailer\Exception;
use Psr\Http\Message\ResponseInterface as Response;

class PostFrontAction extends FrontAction
{
    /**
     * {@inheritDoc}
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws UniqueFailedException
     * @throws Exception
     * @throws NoResultException
     * @throws NoResultException
     * @throws NoResultException
     */
    protected function protectedAction(): Response
    {
        $route = $this->args['route'];
        $menuItem = MenuItem::findByRoute($route);
        if (null !== $menuItem) {
            if (null !== $menuItem->formId) {
                $form = $menuItem->getForm();
                $parsedBody = $this->request->getParsedBody();
                $formHandler = new FormMessageHandler();
                try {
                    /* @noinspection NullPointerExceptionInspection */
                    $formHandler->handleFormPost($form, $parsedBody, $this->request);
                    $form = $menuItem->getForm();

                    return $this->render('theme::form', ['form' => $form, 'success' => true], self::HTTP_FOUND);
                } catch (MissingFieldsException $exception) {
                    return $this->render('theme::form', [
                        'form' => $form,
                        'success' => false,
                        'missingFields' => $exception->fields,
                    ], self::HTTP_BAD_REQUEST);
                }
            }

            return $this->renderMenuItem($menuItem);
        }

        return $this->render('theme::404', [], self::HTTP_NOT_FOUND);
    }
}
