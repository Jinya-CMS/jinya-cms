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
     * @param ReflectionAttribute[] $actionAttributes
     * @param ReflectionAttribute|null $requiredFieldsAttributes
     * @param ReflectionAttribute|null $authenticatedAttributes
     * @param ReflectionAttribute|null $requireOneFieldAttributes
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
        return $this->authenticatedAttributes !== null ? $this->authenticatedAttributes->newInstance() : null;
    }

    public function getRequiredFieldsAttribute(): ?RequiredFields
    {
        return $this->requiredFieldsAttributes !== null ? $this->requiredFieldsAttributes->newInstance() : null;
    }

    public function getRequireOneFieldAttribute(): ?RequireOneField
    {
        return $this->requireOneFieldAttributes !== null ? $this->requireOneFieldAttributes->newInstance() : null;
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