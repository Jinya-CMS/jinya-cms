<?php

namespace App\Web\Actions\Theme;

use App\Database;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Theming;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Exceptions\NoResultException;
use Exception;
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/assets', JinyaAction::PUT)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action compiles the assets of the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully compiled the assets', statusCode: Action::HTTP_NO_CONTENT)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class CompileThemeAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws JsonException
     * @throws NoResultException
     * @throws Exception
     */
    public function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        $theme->compileStyleCache();
        $theme->compileScriptCache();
        $theme->compileAssetCache();

        return $this->noContent();
    }
}
