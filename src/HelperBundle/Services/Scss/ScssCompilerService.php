<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 11.01.2018
 * Time: 18:49
 */

namespace HelperBundle\Services\Scss;


use Leafo\ScssPhp\Compiler;

class ScssCompilerService implements ScssCompilerServiceInterface
{

    /**
     * Compiles the given source path into a css file
     *
     * @param string $code
     * @param string $importPath
     * @param array $variables
     * @return string
     */
    public function compileScss(string $code, string $importPath, array $variables): string
    {
        $scssCompiler = new Compiler();
        $scssCompiler->setVariables($variables);
        $scssCompiler->addImportPath($importPath);

        return $scssCompiler->compile($code);
    }
}