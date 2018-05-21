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
    /** @var string */
    private $contentType;

    /**
     * InvalidContentTypeException constructor.
     * @param string $contentType
     * @param string $message
     */
    public function __construct(string $contentType, string $message)
    {
        $this->contentType = $contentType;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }
}