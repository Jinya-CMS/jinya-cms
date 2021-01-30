<?php

namespace App\OpenApiGeneration;

use App\OpenApiGeneration\Attributes\OpenApiParameter;
use App\OpenApiGeneration\Attributes\OpenApiRequest;
use App\OpenApiGeneration\Attributes\OpenApiRequestBody;
use App\OpenApiGeneration\Attributes\OpenApiResponse;
use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__ROOT__ . '/src/Web/Actions'));
        $classesWithFullPath = [];
        foreach ($files as $file) {
            if ($file->isFile()) {
                $classesWithFullPath[] = $file->getPathname();
            }
        }
        foreach ($classesWithFullPath as $path) {
            $class = 'App' . str_replace('.php', '', implode('\\', explode('/', explode('src', $path)[1])));
            class_exists($class, true);
        }

        $classes = get_declared_classes();
        $openApiEndpoints = [];
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            $actionAttributes = $reflectionClass->getAttributes(JinyaAction::class);
            if (!empty($actionAttributes)) {
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
        $openApiParameterAttributes = $reflectionClass->getAttributes(OpenApiParameter::class);
        $authenticatedAttributes = $reflectionClass->getAttributes(Authenticated::class);
        $openApiRequestJsonBodyAttributes = $reflectionClass->getAttributes(OpenApiRequestBody::class);

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
        if (!empty($authenticatedAttributes)) {
            $result['security'] = ['Jinya Api Key' => new stdClass()];
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
        if (!empty($openApiRequestJsonBodyAttributes)) {
            /** @var OpenApiRequestBody $openApiRequestBody */
            $openApiRequestBody = $openApiRequestJsonBodyAttributes[0]->newInstance();
            $result['requestBody'] = [
                'content' => [
                    'application/json' => [
                        'schema' => $openApiRequestBody->schema,
                        'examples' => [
                            $openApiRequestBody->exampleName => [
                                'value' => $this->generateExample($openApiRequestBody->example),
                            ],
                        ],
                    ],
                ],
            ];
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
                    $response['content'] = [
                        'application/json' => [
                            'schema' => [
                                '$ref' => "#/components/schemas/$openApiResponse->ref",
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
                    $result[$key] = $this->faker->format($item, []);
                } catch (InvalidArgumentException) {
                    $result[$key] = $item;
                }
            }
        }

        return $result;
    }
}