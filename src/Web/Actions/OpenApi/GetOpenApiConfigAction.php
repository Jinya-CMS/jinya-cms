<?php

namespace App\Web\Actions\OpenApi;

use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\EndpointGenerator;
use App\OpenApiGeneration\ModelGenerator;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/system/openapi', JinyaAction::GET)]
class GetOpenApiConfigAction extends Action
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $modelGenerator = new ModelGenerator();
        $models = $modelGenerator->generateModels();
        $endpointGenerator = new EndpointGenerator();
        $endpoints = $endpointGenerator->generateEndpoints();

        $result = [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'Jinya CMS API',
                'version' => INSTALLED_VERSION,
                'description' => 'The OpenAPI specification for your installed Jinya version',
                'contact' => [
                    'name' => 'Jinya Developers',
                    'url' => 'https://github.com/Jinya-CMS/jinya-cms',
                    'email' => 'developers@jinya.de',
                ],
                'license' => [
                    'name' => 'MIT',
                    'url' => 'https://github.com/Jinya-CMS/jinya-CMS/blob/main/LICENSE',
                ],
            ],
            'servers' => [
                [
                    'url' => 'https://' . $this->request->getHeader('host')[0],
                    'description' => 'Your Jinya instance',
                ],
            ],
            'components' => [
                'schemas' => $models,
                'securitySchemes' => [
                    'Jinya Api Key' => [
                        'name' => 'JinyaApiKey',
                        'type' => 'apiKey',
                        'in' => OpenApiParameter::IN_HEADER,
                        'description' => 'A Jinya API Key is generated after a user successfully logged in and returned in the response body of the login response'
                    ]
                ],
            ],
            'paths' => $endpoints,
        ];

        return $this->respond($result, jsonFlags: JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    }
}