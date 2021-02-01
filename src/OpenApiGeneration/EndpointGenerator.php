<?php

namespace App\OpenApiGeneration;

use App\OpenApiGeneration\Attributes\OpenApiArrayResponse;
use App\OpenApiGeneration\Attributes\OpenApiListResponse;
use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiRequestExample;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Utils\ClassResolver;
use App\Web\Actions\Action;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use stdClass;

class EndpointGenerator
{

    private Generator $faker;

    /**
     * EndpointGenerator constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }


    /**
     * @return array
     * @throws ReflectionException
     */
    public function generateEndpoints(): array
    {
        $classes = ClassResolver::loadClasses(__ROOT__ . '/src/Web/Actions');

        $openApiEndpoints = [];
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            $actionAttributes = $reflectionClass->getAttributes(JinyaAction::class);
            $openApiRequestAttributes = $reflectionClass->getAttributes(OpenApiRequest::class);
            if (!empty($actionAttributes) && !empty($openApiRequestAttributes)) {
                foreach ($actionAttributes as $actionAttribute) {
                    /** @var JinyaAction $action */
                    $action = $actionAttribute->newInstance();
                    $foundEndpoints = array_filter(
                        $openApiEndpoints,
                        static fn(OpenApiEndpoint $endpoint) => $endpoint->url === $action->url
                    );
                    if (empty($foundEndpoints)) {
                        $foundEndpoint = new OpenApiEndpoint($action->url);
                    } else {
                        $foundEndpoint = array_values($foundEndpoints)[0];
                    }
                    $foundEndpoint->methods[$action->method] = new OpenApiMethod($reflectionClass);
                    if (empty($foundEndpoints)) {
                        $openApiEndpoints[] = $foundEndpoint;
                    }
                }
            }
        }

        $result = [];
        foreach ($openApiEndpoints as $openApiEndpoint) {
            $result[$openApiEndpoint->url] = $this->generateEndpoint($openApiEndpoint);
        }

        ksort($result);

        return $result;
    }

    public function generateEndpoint(OpenApiEndpoint $openApiEndpoint): array
    {
        $items = [];
        foreach ($openApiEndpoint->methods as $key => $openApiMethod) {
            $items[strtolower($key)] = $this->generateMethod($openApiMethod);
        }

        ksort($items);

        return $items;
    }

    public function generateMethod(OpenApiMethod $openApiMethod): array
    {
        $reflectionClass = $openApiMethod->reflectionClass;
        $openApiRequestAttributes = $reflectionClass->getAttributes(OpenApiRequest::class);
        $openApiResponseAttributes = $reflectionClass->getAttributes(OpenApiResponse::class);
        $openApiListResponseAttributes = $reflectionClass->getAttributes(OpenApiListResponse::class);
        $openApiArrayResponseAttributes = $reflectionClass->getAttributes(OpenApiArrayResponse::class);
        $openApiParameterAttributes = $reflectionClass->getAttributes(OpenApiParameter::class);
        $authenticatedAttributes = $reflectionClass->getAttributes(Authenticated::class);
        $openApiRequestBodyAttributes = $reflectionClass->getAttributes(OpenApiRequestBody::class);
        $openApiRequestExampleAttributes = $reflectionClass->getAttributes(OpenApiRequestExample::class);
        $requiredFieldsAttributes = $reflectionClass->getAttributes(RequiredFields::class);

        $result = [];
        if (!empty($openApiRequestAttributes)) {
            /** @var OpenApiRequest $openApiRequest */
            $openApiRequest = $openApiRequestAttributes[0]->newInstance();
            $result['summary'] = $openApiRequest->summary;
            $result['operationId'] = $this->convertToSnakeCase($reflectionClass->getShortName());
            if ($openApiRequest->binary) {
                $result['requestBody'] = [
                    'content' => [
                        'application/octet-stream' => new stdClass(),
                    ],
                ];
            }
        }
        if (!empty($openApiParameterAttributes)) {
            $result['parameters'] = [];
            foreach ($openApiParameterAttributes as $openApiParameterAttribute) {
                /** @var OpenApiParameter $openApiParameter */
                $openApiParameter = $openApiParameterAttribute->newInstance();
                $result['parameters'][] = [
                    'name' => $openApiParameter->name,
                    'in' => $openApiParameter->in,
                    'required' => $openApiParameter->required,
                    'schema' => [
                        'type' => $openApiParameter->type,
                    ],
                ];
            }
        }
        if (!empty($openApiRequestBodyAttributes)) {
            /** @var OpenApiRequestBody $openApiRequestBody */
            $openApiRequestBody = $openApiRequestBodyAttributes[0]->newInstance();
            $result['requestBody'] = [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => $openApiRequestBody->schema,
                        ],
                        'examples' => [],
                    ],
                ],
            ];
            if (!empty($requiredFieldsAttributes)) {
                /** @var RequiredFields $requiredFields */
                $requiredFields = $requiredFieldsAttributes[0]->newInstance();
                $result['requestBody']['content']['application/json']['schema']['required'] = $requiredFields->requiredFields;
            }
            if (!empty($openApiRequestExampleAttributes)) {
                foreach ($openApiRequestExampleAttributes as $openApiRequestExampleAttribute) {
                    /** @var OpenApiRequestExample $openApiRequestExample */
                    $openApiRequestExample = $openApiRequestExampleAttribute->newInstance();
                    $result['requestBody']['content']['application/json']['examples'][$openApiRequestExample->name] = [
                        'value' => $this->generateExample(
                            $openApiRequestExample->example
                        )
                    ];
                }
            }
        }
        if (!empty($openApiResponseAttributes)) {
            $result['responses'] = [];
            foreach ($openApiResponseAttributes as $openApiResponseAttribute) {
                /** @var OpenApiResponse $openApiResponse */
                $openApiResponse = $openApiResponseAttribute->newInstance();
                $response = [
                    'description' => $openApiResponse->description,
                ];
                if (!empty($openApiResponse->ref)) {
                    $refPath = explode('\\', $openApiResponse->ref);
                    $refPath = array_reverse($refPath);
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/$refPath[0]",
                            ],
                        ],
                    ];
                } elseif (!empty($openApiResponse->schema)) {
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => $openApiResponse->schema,
                            ],
                        ],
                    ];
                }
                if (!empty($openApiResponse->example)) {
                    $response['content']['application/json']['examples'] = [
                        $openApiResponse->exampleName => [
                            'value' => $this->generateExample($openApiResponse->example),
                        ],
                    ];
                }

                $result['responses'][(string)$openApiResponse->statusCode] = $response;
            }
        }
        if (!empty($openApiListResponseAttributes)) {
            $result['responses'] = $result['responses'] ?? [];
            foreach ($openApiListResponseAttributes as $openApiListResponseAttribute) {
                /** @var OpenApiResponse $openApiListResponse */
                $openApiListResponse = $openApiListResponseAttribute->newInstance();
                $response = [
                    'description' => $openApiListResponse->description,
                ];
                if (!empty($openApiListResponse->ref)) {
                    $refPath = explode('\\', $openApiListResponse->ref);
                    $refPath = array_reverse($refPath);
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'items' => [
                                        'type' => 'array',
                                        'items' => [
                                            '$ref' => "#/components/schemas/$refPath[0]",
                                        ],
                                    ],
                                    'itemsCount' => [
                                        'type' => 'integer',
                                    ],
                                    'totalCount' => [
                                        'type' => 'integer',
                                    ],
                                    'offset' => [
                                        'type' => 'integer',
                                    ]
                                ],
                            ],
                        ],
                    ];
                } elseif (!empty($openApiListResponse->schema)) {
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'items' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => $openApiListResponse->schema,
                                        ],
                                    ],
                                    'itemsCount' => [
                                        'type' => 'integer',
                                    ],
                                    'totalCount' => [
                                        'type' => 'integer',
                                    ],
                                    'offset' => [
                                        'type' => 'integer',
                                    ]
                                ],
                            ],
                        ],
                    ];
                }
                if (!empty($openApiListResponse->example)) {
                    $exampleItems = [];
                    for ($i = 0; $i < random_int(5, 20); $i++) {
                        $exampleItems[] = $this->generateExample($openApiListResponse->example);
                    }
                    $response['content']['application/json']['examples'] = [
                        $openApiListResponse->exampleName => [
                            'value' => [
                                'offset' => 0,
                                'itemsCount' => count($exampleItems),
                                'totalCount' => count($exampleItems),
                                'items' => $exampleItems,
                            ],
                        ],
                    ];
                }

                $result['responses'][(string)$openApiListResponse->statusCode] = $response;
            }
        }
        if (!empty($openApiArrayResponseAttributes)) {
            $result['responses'] = $result['responses'] ?? [];
            foreach ($openApiArrayResponseAttributes as $openApiArrayResponseAttribute) {
                /** @var OpenApiResponse $openApiArrayResponse */
                $openApiArrayResponse = $openApiArrayResponseAttribute->newInstance();
                $response = [
                    'description' => $openApiArrayResponse->description,
                ];
                if (!empty($openApiArrayResponse->ref)) {
                    $refPath = explode('\\', $openApiArrayResponse->ref);
                    $refPath = array_reverse($refPath);
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [
                                    '$ref' => "#/components/schemas/$refPath[0]",
                                ],
                            ],
                        ],
                    ];
                } elseif (!empty($openApiArrayResponse->schema)) {
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                'type' => 'array',
                                'items' => [
                                    'type' => 'object',
                                    'properties' => $openApiArrayResponse->schema,
                                ],
                            ],
                        ],
                    ];
                }
                if (!empty($openApiArrayResponse->example)) {
                    $exampleItems = [];
                    for ($i = 0; $i < random_int(5, 20); $i++) {
                        $exampleItems[] = $this->generateExample($openApiArrayResponse->example);
                    }
                    $response['content']['application/json']['examples'] = [
                        $openApiArrayResponse->exampleName => [
                            'value' => $exampleItems,
                        ],
                    ];
                }

                $result['responses'][(string)$openApiArrayResponse->statusCode] = $response;
            }
        }
        if (!empty($authenticatedAttributes)) {
            $result['security'] = [['Jinya-Api-Key' => []]];
            $result['responses'] = $result['responses'] ?? [];
            $result['responses'][Action::HTTP_FORBIDDEN] = [
                'description' => 'Api key invalid',
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => OpenApiResponse::EXCEPTION_SCHEMA,
                        ],
                        'examples' => [
                            'Not authenticated' => [
                                'value' => [
                                    'success' => false,
                                    'error' => [
                                        'message' => 'Api key invalid',
                                        'type' => 'HttpForbiddenException',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $result;
    }

    private function convertToSnakeCase(string $input): string
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match === strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode('_', $ret);
    }

    private function generateExample(array $example): array
    {
        $result = [];

        foreach ($example as $key => $item) {
            if (is_array($item)) {
                $result[$key] = $this->generateExample($item);
            }
            if (is_string($item)) {
                try {
                    if ($item === OpenApiResponse::FAKER_NUMERIFY) {
                        $result[$key] = $this->faker->numerify('######');
                    } else {
                        $result[$key] = $this->faker->format($item, []);
                    }
                } catch (InvalidArgumentException) {
                    $result[$key] = $item;
                }
            }
        }

        return $result;
    }
}