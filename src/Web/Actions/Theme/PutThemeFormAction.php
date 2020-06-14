<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\Theme;
use App\Database\ThemeForm;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class PutThemeFormAction extends ThemeAction
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $this->syncThemes();
        $themeId = $this->args['id'];
        $name = $this->args['name'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $body = $this->request->getParsedBody();
        $formId = $body['form'];
        $form = Form::findById($formId);
        if (!$form) {
            throw new NoResultException($this->request, 'Form not found');
        }

        $themeForm = ThemeForm::findByThemeAndName($themeId, $name);
        $themeForm->themeId = $themeId;
        $themeForm->formId = $form;
        $themeForm->name = $name;
        $themeForm->update();

        return $this->noContent();
    }
}