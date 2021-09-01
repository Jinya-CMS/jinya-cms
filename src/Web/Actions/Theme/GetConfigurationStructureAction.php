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
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/theme/{id}/configuration/structure', JinyaAction::GET)]
#[Authenticated(Authenticated::WRITER)]
#[OpenApiRequest('This action gets the configuration structure of the given theme')]
#[OpenApiParameter('id', required: true, type: OpenApiParameter::TYPE_INTEGER)]
#[OpenApiResponse('Successfully got the configuration', example: [
    'title' => 'Configure Jinya default theme',
    'groups' => [
        [
            'name' => 'page',
            'title' => 'Page',
            'fields' => [
                [
                    'name' => 'title',
                    'type' => 'string',
                    'label' => 'Title',
                ],
            ],
        ],
        [
            'name' => 'footer',
            'title' => 'Footer',
            'fields' => [
                [
                    'name' => 'copyright',
                    'type' => 'string',
                    'label' => 'Copyright message',
                ],
            ],
        ],
        [
            'name' => 'fonts',
            'title' => 'Font links',
            'fields' => [
                [
                    'name' => 'menu',
                    'type' => 'string',
                    'label' => 'Menu entries',
                ],
                [
                    'name' => 'heading',
                    'type' => 'string',
                    'label' => 'Headings',
                ],
                [
                    'name' => 'paragraph',
                    'type' => 'string',
                    'label' => 'Text',
                ],
                [
                    'name' => 'brand',
                    'type' => 'string',
                    'label' => 'Branding',
                ],
            ],
        ],
        [
            'name' => 'input',
            'title' => 'Input field options',
            'fields' => [
                [
                    'name' => 'optional',
                    'type' => 'string',
                    'label' => 'Mark optional fields',
                ],
            ],
        ],
        [
            'name' => 'buttons',
            'title' => 'Button labels',
            'fields' => [
                [
                    'name' => 'submit',
                    'type' => 'string',
                    'label' => 'Submit',
                ],
            ],
        ],
        [
            'name' => 'dropdowns',
            'title' => 'Dropdowns',
            'fields' => [
                [
                    'name' => 'placeholder',
                    'type' => 'string',
                    'label' => 'Placeholder',
                ],
            ],
        ],
        [
            'name' => 'messages',
            'title' => 'Messages',
            'fields' => [
                [
                    'name' => 'mail_sent_message',
                    'type' => 'string',
                    'label' => 'Mail sent message',
                ],
                [
                    'name' => 'mail_not_sent_message',
                    'type' => 'string',
                    'label' => 'Mail not sent message',
                ],
            ],
        ],
        [
            'name' => 'profile',
            'title' => 'Profile pages',
            'fields' => [
                [
                    'name' => 'show_email',
                    'type' => 'boolean',
                    'label' => 'Show mail address',
                ],
            ],
        ],
    ],
    'links' => [
        'segment_pages' => [
            'startpage' => 'Homepage',
        ],
        'menus' => [
            'primary' => 'Main menu',
            'footer' => 'Footer menu',
            'shadow' => 'Invisible links',
        ],
        'files' => [
            'faviconSmall' => 'Small favicon 64x64',
            'faviconShortcutIcon' => 'Small favicon in ico file format',
            'faviconLarge' => 'Big favicon 512x512',
        ],
    ],
], exampleName: 'Configuration for jinya default theme', schema: [
    'title' => ['type' => 'string'],
    'groups' => [
        'type' => 'array',
        'items' => [
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'title' => ['type' => 'string'],
                'fields' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => [
                            'name' => ['type' => 'string'],
                            'type' => [
                                'type' => 'string',
                                'enum' => [
                                    'string',
                                    'boolean',
                                ],
                            ],
                            'label' => ['type' => 'string'],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'links' => [
        'type' => 'object',
        'properties' => [
            'files' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            'menus' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            'pages' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            'segmentPages' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            'forms' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
            'galleries' => ['type' => 'object', 'additionalProperties' => ['type' => 'string']],
        ],
    ],
])]
#[OpenApiResponse('Theme not found', example: OpenApiResponse::NOT_FOUND, exampleName: 'Theme not found', statusCode: Action::HTTP_NOT_FOUND, schema: OpenApiResponse::EXCEPTION_SCHEMA)]
class GetConfigurationStructureAction extends ThemeAction
{
    /**
     * {@inheritDoc}
     * @throws Database\Exceptions\ForeignKeyFailedException
     * @throws Database\Exceptions\InvalidQueryException
     * @throws Database\Exceptions\UniqueFailedException
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

        return $this->respond($theme->getConfigurationStructure());
    }
}
