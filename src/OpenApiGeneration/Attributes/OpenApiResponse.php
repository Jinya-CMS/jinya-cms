<?php

namespace App\OpenApiGeneration\Attributes;

use App\Web\Actions\Action;
use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class OpenApiResponse
{
    public ?string $ref = null;
    public array $structure = [];

    public function __construct(
        public string $description,
        public array $sample,
        public int $statusCode = Action::HTTP_OK,
    ) {
    }
}