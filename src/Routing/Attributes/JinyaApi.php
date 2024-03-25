<?php

namespace App\Routing\Attributes;

use App\Authentication\AuthenticationChecker;
use Attribute;

/**
 * This attribute is used to make an entity class routable and available via the REST API
 */
#[Attribute(flags: Attribute::TARGET_CLASS)]
class JinyaApi
{
    /**
     * Creates a new instance of JinyaApi
     *
     * @param bool $createEnabled If true, an endpoint for POST request is created
     * @param string $createRole Sets the role needed to access the create endpoint
     * @param bool $readEnabled If true, an endpoint for GET request is created
     * @param string $readRole Sets the role needed to access the read endpoint
     * @param bool $updateEnabled If true, an endpoint for PUT request is created
     * @param string $updateRole Sets the role needed to access the update endpoint
     * @param bool $deleteEnabled If true, an endpoint for DELETE request is created
     * @param string $deleteRole Sets the role needed to access the delete endpoint
     */
    public function __construct(
        public readonly bool $createEnabled = true,
        public readonly string $createRole = AuthenticationChecker::ROLE_WRITER,
        public readonly bool $readEnabled = true,
        public readonly string $readRole = AuthenticationChecker::ROLE_READER,
        public readonly bool $updateEnabled = true,
        public readonly string $updateRole = AuthenticationChecker::ROLE_WRITER,
        public readonly bool $deleteEnabled = true,
        public readonly string $deleteRole = AuthenticationChecker::ROLE_WRITER,
    ) {
    }
}
