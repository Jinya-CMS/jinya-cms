<?php

namespace App\OpenApiGeneration;

use App\OpenApiGeneration\Attributes\OpenApiAdditionalField;
use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use App\OpenApiGeneration\Attributes\OpenApiRecursiveField;
use App\OpenApiGeneration\Exceptions\OpenApiModelException;
use App\Utils\ClassResolver;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class ModelGenerator
{
    private const TYPE_OBJECT = 'object';

    /**
     * @return array
     * @throws OpenApiModelException
     * @throws ReflectionException
     */
    public function generateModels(): array
    {
        $classes = ClassResolver::loadClasses(__ROOT__ . '/src/Database');
        $result = [];
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            if (!empty($reflectionClass->getAttributes(OpenApiModel::class))) {
                $model = $this->generateModel($reflectionClass);
                $result[$model['title']] = $model;
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * @param ReflectionClass $reflectionClass
     * @return array
     * @throws OpenApiModelException
     */
    #[ArrayShape([
        'title' => "string",
        'type' => "string",
        'properties' => "array",
        'description' => "string"
    ])] public function generateModel(
        ReflectionClass $reflectionClass
    ): array {
        $openApiModelAttributes = $reflectionClass->getAttributes(OpenApiModel::class);
        if (empty($openApiModelAttributes)) {
            throw new OpenApiModelException('No OpenApiModel attribute found');
        }

        $result = [
            'title' => $reflectionClass->getShortName(),
            'type' => self::TYPE_OBJECT,
        ];

        /** @var OpenApiModel $attribute */
        $attribute = $openApiModelAttributes[0]->newInstance();

        $properties = $reflectionClass->getProperties();

        $result['description'] = $attribute->description;
        $openApiProperties = [];
        $requiredFields = [];

        foreach ($properties as $property) {
            $openApiProperty = [];
            $openApiHiddenAttributes = $property->getAttributes(OpenApiHiddenField::class);
            if (!empty($openApiHiddenAttributes)) {
                continue;
            }

            $openApiFieldAttributes = $property->getAttributes(OpenApiField::class);
            $propertyType = $property->getType();
            if ($propertyType instanceof ReflectionNamedType) {
                $openApiProperty['type'] = match ($propertyType->getName()) {
                    'int' => 'integer',
                    'bool' => 'boolean',
                    DateTime::class => 'string',
                    default => $propertyType->getName()
                };
                if (!empty($openApiFieldAttributes)) {
                    /** @var OpenApiField $fieldAttribute */
                    $fieldAttribute = $openApiFieldAttributes[0]->newInstance();

                    if ($fieldAttribute->required) {
                        if ($fieldAttribute !== null && $fieldAttribute->name !== null) {
                            $requiredFields[] = $fieldAttribute->name;
                        } else {
                            $requiredFields[] = $property->getName();
                        }
                    }

                    if ($fieldAttribute->defaultValue !== null) {
                        $openApiProperty['default'] = $fieldAttribute->defaultValue;
                    }

                    if ($fieldAttribute->format !== null) {
                        $openApiProperty['format'] = $fieldAttribute->format;
                    }

                    if ($fieldAttribute->enumValues !== null) {
                        $openApiProperty['enum'] = $fieldAttribute->enumValues;
                    }

                    if ($fieldAttribute->array && !empty($fieldAttribute->arrayRef)) {
                        $refPath = explode('\\', $fieldAttribute->arrayRef);
                        $refPath = array_reverse($refPath);
                        $openApiProperty['type'] = 'array';
                        $openApiProperty['items'] = ['$ref' => "#/components/schemas/$refPath[0]"];
                    } elseif ($fieldAttribute->array && !empty($fieldAttribute->arrayType)) {
                        $openApiProperty['type'] = 'array';
                        $openApiProperty['items'] = ['type' => $fieldAttribute->arrayType];
                    }

                    if ($fieldAttribute->object) {
                        $openApiProperty['type'] = self::TYPE_OBJECT;
                        if ($fieldAttribute->objectRef !== null) {
                            $refPath = explode('\\', $fieldAttribute->objectRef);
                            $refPath = array_reverse($refPath);
                            $openApiProperty['$ref'] = "#/components/schemas/$refPath[0]";
                        }
                    }

                    if (!empty($fieldAttribute->structure)) {
                        $openApiProperty['type'] = self::TYPE_OBJECT;
                        $openApiProperty['properties'] = $fieldAttribute->structure;
                    }
                } else {
                    $fieldAttribute = null;
                }
                if ($fieldAttribute !== null && $fieldAttribute->name !== null) {
                    $openApiProperties[$fieldAttribute->name] = $openApiProperty;
                } else {
                    $openApiProperties[$property->getName()] = $openApiProperty;
                }
            }
        }

        $openApiRecursiveFieldAttributes = $reflectionClass->getAttributes(OpenApiRecursiveField::class);
        if (!empty($openApiRecursiveFieldAttributes)) {
            foreach ($openApiRecursiveFieldAttributes as $openApiRecursiveFieldAttribute) {
                /** @var OpenApiRecursiveField $openApiRecursiveField */
                $openApiRecursiveField = $openApiRecursiveFieldAttribute->newInstance();
                $name = $reflectionClass->getShortName();
                $openApiProperties[$openApiRecursiveField->name] = [
                    'items' => ['$ref' => "#/components/schemas/$name"],
                    'type' => 'array',
                ];
            }
        }

        $openApiAdditionalFieldAttributes = $reflectionClass->getAttributes(OpenApiAdditionalField::class);
        if (!empty($openApiAdditionalFieldAttributes)) {
            foreach ($openApiAdditionalFieldAttributes as $openApiAdditionalFieldAttribute) {
                /** @var OpenApiAdditionalField $openApiAdditionalField */
                $openApiAdditionalField = $openApiAdditionalFieldAttribute->newInstance();
                $openApiProperties[$openApiAdditionalField->name] = ['type' => $openApiAdditionalField->type];
            }
        }

        if ($attribute->hasId) {
            $openApiProperties['id'] = [
                'type' => $attribute->idType,
            ];
            $requiredFields[] = 'id';
        }
        $result['properties'] = $openApiProperties;
        $result['required'] = $requiredFields;

        return $result;
    }
}