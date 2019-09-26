<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 11.01.2018
 * Time: 18:49
 */

namespace Jinya\Services\Scss;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Compressed;

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
        $scssCompiler->setFormatter(Compressed::class);

        return $scssCompiler->compile($code);
    }
}
