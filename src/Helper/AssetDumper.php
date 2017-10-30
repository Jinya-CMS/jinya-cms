<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 30.10.2017
 * Time: 16:27
 */

namespace Helper;


use Assetic\Asset\AssetInterface;
use Assetic\AssetManager;
use Assetic\Util\VarUtils;

class AssetDumper
{
    /** @var AssetManager */
    private $am;

    private $asseticVariables;
    /** @var string */
    private $basePath;

    /**
     * AssetDumper constructor.
     * @param AssetManager $am
     * @param $asseticVariables
     * @param string $basePath
     */
    public function __construct(AssetManager $am, $asseticVariables, $basePath)
    {
        $this->am = $am;
        $this->asseticVariables = $asseticVariables;
        $this->basePath = $basePath;
    }

    public function dumpAssets()
    {
        foreach ($this->am->getNames() as $name) {
            $this->dumpAsset($name);
        }
    }

    private function dumpAsset(string $name)
    {
        $asset = $this->am->get($name);
        $formula = $this->am->hasFormula($name) ? $this->am->getFormula($name) : array();

        // start by dumping the main asset
        $this->doDump($asset);

        $debug = isset($formula[2]['debug']) ? $formula[2]['debug'] : $this->am->isDebug();
        $combine = isset($formula[2]['combine']) ? $formula[2]['combine'] : !$debug;

        // dump each leaf if no combine
        if (!$combine) {
            foreach ($asset as $leaf) {
                $this->doDump($leaf);
            }
        }
    }

    private function doDump(AssetInterface $asset)
    {
        $combinations = VarUtils::getCombinations(
            $asset->getVars(),
            $this->asseticVariables
        );

        foreach ($combinations as $combination) {
            $asset->setValues($combination);

            // resolve the target path
            $target = rtrim($this->basePath, '/') . '/' . $asset->getTargetPath();
            $target = str_replace('_controller/', '', $target);
            $target = VarUtils::resolve($target, $asset->getVars(), $asset->getValues());

            if (!is_dir($dir = dirname($target))) {
                if (false === @mkdir($dir, 0777, true)) {
                    throw new \RuntimeException('Unable to create directory ' . $dir);
                }
            }

            if (false === @file_put_contents($target, $asset->dump())) {
                throw new \RuntimeException('Unable to write file ' . $target);
            }
        }
    }
}