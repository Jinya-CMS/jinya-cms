<?php

namespace App\Web\Actions\Frontend;

use App\Database\MenuItem;
use App\Messaging\FormMessageHandler;
use App\Web\Exceptions\MissingFieldsException;
use Psr\Http\Message\ResponseInterface as Response;

class PostFrontAction extends FrontAction
{

    /**
     * @inheritDoc
     */
    protected function protectedAction(): Response
    {
        $route = $this->args['route'];
        $menuItem = MenuItem::findByRoute($route);
        if ($menuItem !== null) {
            if ($menuItem->formId !== null) {
                $form = $menuItem->getForm();
                $parsedBody = $this->request->getParsedBody();
                $formHandler = new FormMessageHandler($this->request, $this->engine);
                try {
                    $formHandler->handleFormPost($form, $parsedBody);
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

        return $this->render('theme::not-found', [], self::HTTP_NOT_FOUND);
    }
}