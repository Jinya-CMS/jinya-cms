<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 29.10.2017
 * Time: 19:44
 */

namespace InstallBundle\Components;


use Symfony\Component\Yaml\Parser;
use Symfony\Component\Yaml\Yaml;

class ParameterProcessor
{
    public static function processParameters(array $parameters, string $rootPath)
    {
        $yamlParser = new Parser();
        $parsedParameters = $yamlParser->parseFile($rootPath . '/config/parameters.yml');

        $mergedParameters = array_merge($parsedParameters, $parameters);
        Yaml::dump($rootPath . '/config/parameters.yml', $mergedParameters);
    }
}