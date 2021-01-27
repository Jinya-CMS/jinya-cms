<?php

namespace App\OpenApiGeneration;

use App\OpenApiGeneration\Attributes\OpenApiField;
use App\OpenApiGeneration\Attributes\OpenApiHiddenField;
use App\OpenApiGeneration\Attributes\OpenApiModel;
use App\OpenApiGeneration\Exceptions\OpenApiModelException;
use Iterator;
use JetBrains\PhpStorm\ArrayShape;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;

class ModelGenerator
{
    /**
     * @return Iterator
     * @throws OpenApiModelException
     * @throws ReflectionException
     */
    public function generateModels(): Iterator
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__ROOT__ . '/src/Database'));
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
        foreach ($classes as $class) {
            $reflectionClass = new ReflectionClass($class);
            if (!empty($reflectionClass->getAttributes(OpenApiModel::class))) {
                yield $this->generateModel($reflectionClass);
            }
        }
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
            'type' => 'object',
        ];

        /** @var OpenApiModel $attribute */
        $attribute = $openApiModelAttributes[0]->newInstance();

        $properties = $reflectionClass->getProperties();

        $result['description'] = $attribute->description;
        $openApiProperties = [];

        foreach ($properties as $property) {
            $openApiProperty = [];
            $openApiHiddenAttributes = $property->getAttributes(OpenApiHiddenField::class);
            if (!empty($openApiHiddenAttributes)) {
                continue;
            }

            $openApiFieldAttributes = $property->getAttributes(OpenApiField::class);
            $propertyType = $property->getType();
            if ($propertyType instanceof ReflectionNamedType) {
                $openApiProperty['type'] = $propertyType->getName();
                if (!empty($openApiFieldAttributes)) {
                    /** @var OpenApiField $fieldAttribute */
                    $fieldAttribute = $openApiFieldAttributes[0]->newInstance();

                    if ($fieldAttribute->required !== null) {
                        $openApiProperty['required'] = $fieldAttribute->required;
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
                        $openApiProperties['items'] = ['$ref' => "#/components/schemas/$refPath[0]"];
                    }
                }
                $openApiProperties[$property->getName()] = $openApiProperty;
            }
        }

        if ($attribute->hasId) {
            $openApiProperties['id'] = [
                'type' => $attribute->idType,
                'required' => true,
            ];
        }
        $result['properties'] = $openApiProperties;

        return $result;
    }
}