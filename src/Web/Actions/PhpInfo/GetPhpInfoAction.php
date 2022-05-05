<?php

namespace App\Web\Actions\PhpInfo;

use App\Maintenance\PhpInfo\PhpInfoService;
use App\Web\Actions\Action;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionException;

class GetPhpInfoAction extends Action
{
    /**
     * {@inheritDoc}
     * @throws ReflectionException
     */
    protected function action(): Response
    {
        $phpInfoService = new PhpInfoService();

        return $this->respond([
            'apache' => [
                'modules' => $phpInfoService->getApacheModules(),
                'version' => $phpInfoService->getApacheVersion(),
            ],
            'system' => [
                'uname' => $phpInfoService->getUname(),
            ],
            'php' => [
                'version' => $phpInfoService->getVersion(),
                'extensions' => $phpInfoService->getLoadedExtensions(),
            ],
            'zend' => [
                'version' => $phpInfoService->getZendVersion(),
            ],
        ]);
    }
}
