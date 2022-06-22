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
use Throwable;

/**
 * Action to post the forms
 */
class PostFrontAction extends FrontAction
{
    /**
     * Executes the form post. If the form is not found the 404 page will be returned
     *
     * @return Response
     * @throws Exception
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws Throwable
     */
    protected function protectedAction(): Response
    {
        $route = $this->args['route'];
        $menuItem = MenuItem::findByRoute($route);
        if ($menuItem !== null) {
            if ($menuItem->formId !== null) {
                $form = $menuItem->getForm();
                $formHandler = new FormMessageHandler();
                try {
                    if ($form !== null) {
                        $formHandler->handleFormPost($form, $this->body, $this->request);
                    }

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
