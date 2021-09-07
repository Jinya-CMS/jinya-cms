<?php

namespace App\Web\Routes;

use App\Web\Attributes\Authenticated;
use App\Web\Attributes\JinyaAction;
use App\Web\Attributes\RequiredFields;
use App\Web\Attributes\RequireOneField;
use Iterator;
use ReflectionAttribute;

class Route
{
    /**
     * Route constructor.
     * @param ReflectionAttribute<JinyaAction>[] $actionAttributes
     * @param ReflectionAttribute<RequiredFields>|null $requiredFieldsAttributes
     * @param ReflectionAttribute<Authenticated>|null $authenticatedAttributes
     * @param ReflectionAttribute<RequireOneField>|null $requireOneFieldAttributes
     * @param string $class
     */
    public function __construct(
        public array $actionAttributes,
        public ?ReflectionAttribute $requiredFieldsAttributes,
        public ?ReflectionAttribute $authenticatedAttributes,
        public ?ReflectionAttribute $requireOneFieldAttributes,
        public string $class,
    ) {
    }

    public function getAuthenticatedAttribute(): ?Authenticated
    {
        return $this->authenticatedAttributes?->newInstance();
    }

    public function getRequiredFieldsAttribute(): ?RequiredFields
    {
        return $this->requiredFieldsAttributes?->newInstance();
    }

    public function getRequireOneFieldAttribute(): ?RequireOneField
    {
        return $this->requireOneFieldAttributes?->newInstance();
    }

    /**
     * @return Iterator<JinyaAction>
     */
    public function getActions(): Iterator
    {
        foreach ($this->actionAttributes as $actionAttribute) {
            yield $actionAttribute->newInstance();
        }
    }
}