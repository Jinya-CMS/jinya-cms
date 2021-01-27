<?php

namespace App\Web\Actions\OpenApi;

use App\OpenApiGeneration\ModelGenerator;
use App\Web\Actions\Action;
use App\Web\Attributes\JinyaAction;
use Psr\Http\Message\ResponseInterface as Response;

#[JinyaAction('/api/system/openapi', JinyaAction::GET)]
class GetOpenApiConfigAction extends Action
{
    /**
     * {@inheritDoc}
     */
    protected function action(): Response
    {
        $modelGenerator = new ModelGenerator();
        $models = $modelGenerator->generateModels();

        return $this->respond(iterator_to_array($models));
    }
}
