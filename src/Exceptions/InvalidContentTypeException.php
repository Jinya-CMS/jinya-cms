<?php
/**
 * Created by PhpStorm.
 * User: imanu
 * Date: 17.02.2018
 * Time: 20:02
 */

namespace Jinya\Exceptions;

use Exception;

class InvalidContentTypeException extends Exception
{
    private string $contentType;

    /**
     * InvalidContentTypeException constructor.
     */
    public function __construct(string $contentType, string $message)
    {
        parent::__construct($message);
        $this->contentType = $contentType;
        $this->message = $message;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }
}
