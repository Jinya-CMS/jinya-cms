<?php

namespace App\Web\Actions\Theme;

use App\Database\Theme;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

class GetThemeFormAction extends ThemeAction
{

    /**
     * @return Response
     * @throws NoResultException
     * @throws JsonException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $theme = Theme::findById($themeId);

        if (!$theme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $forms = $theme->getForms();
        $result = [];

        foreach ($forms as $key => $form) {
            $result[$key] = $form->format();
        }

        return $this->respond($result);
    }
}