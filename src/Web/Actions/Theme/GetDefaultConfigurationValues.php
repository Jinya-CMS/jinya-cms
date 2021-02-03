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
use JsonException;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/configuration/default', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the default configuration of the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the default configuration', example: [
    'page' => [
        'title' => 'Jinya CMS',
    ],
    'footer' => [
        'copyright' => null,
    ],
    'fonts' => [
        'menu' => 'https://fonts.googleapis.com/css?family=Open+Sans',
        'heading' => 'https://fonts.googleapis.com/css?family=Raleway:300,400',
        'paragraph' => 'https://fonts.googleapis.com/css?family=Open+Sans',
        'brand' => 'https://fonts.googleapis.com/css?family=Josefin+Sans',
    ],
    'input' => [
        'optional' => ' (optional)',
    ],
    'buttons' => [
        'submit' => 'Submit',
    ],
    'dropdowns' => [
        'placeholder' => 'Please choose...',
    ],
    'messages' => [
        'mail_sent_message' => 'Mail was sent successfully',
        'mail_not_sent_message' => 'Mail could not be sent',
    ],
    'profile' => [
        'show_email' => false,
    ],
], exampleName: 'Default configuration for jinya default theme', map: true)]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetDefaultConfigurationValues extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
     * @throws JsonException
     * @throws NoResultException
     */
    protected function action(): Response
    {
        $themeId = $this->args['id'];
        $dbTheme = Database\Theme::findById($themeId);
        if (!$dbTheme) {
            throw new NoResultException($this->request, 'Theme not found');
        }

        $theme = new Theming\Theme($dbTheme);
        $config = $theme->getConfigurationValues();

        return $this->respond($config);
    }
}
