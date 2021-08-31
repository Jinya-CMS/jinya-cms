<?php

namespace App\Web\Actions\Theme;

use App\Database\Exceptions\ForeignKeyFailedException;
use App\Database\Exceptions\InvalidQueryException;
use App\Database\Exceptions\UniqueFailedException;
use App\Database\Form;
use App\Database\Theme;
use App\Database\ThemeForm;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/form/{name}', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action updates the given theme form')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiParameter('name', required: true, type: OpenApiParameter::TYPE_STRING)]
#[OpenApiRequestBody(['form' => ['type' => 'integer']])]
#[OpenApiResponse('Successfully updated the theme form', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Theme or form not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme or form not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class PutThemeFormAction extends ThemeAction
{
    /**
     * @throws JsonException
     * @throws NoResultException
     * @throws UniqueFailedException
     * @throws ForeignKeyFailedException
     * @throws InvalidQueryException
     */
    public function action(): Response
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
        if (null !== $themeForm) {
            $themeForm->themeId = $themeId;
            $themeForm->formId = $form->id;
            $themeForm->name = $name;
            $themeForm->update();
        } else {
            $themeForm = new ThemeForm();
            $themeForm->themeId = $themeId;
            $themeForm->formId = $form->id;
            $themeForm->name = $name;
            $themeForm->create();
        }

        return $this->noContent();
    }
}
