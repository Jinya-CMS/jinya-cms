<?php

namespace App\OpenApiGeneration\Attributes;

use App\Web\Actions\Action;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class OpenApiListResponse
{
    public function __construct(
        public string $description,
        public array $example = [],
        public string $exampleName = '',
        public int $statusCode = Action::HTTP_OK,
        public string $ref = '',
        public array $schema = [],
    ) {
    }
}