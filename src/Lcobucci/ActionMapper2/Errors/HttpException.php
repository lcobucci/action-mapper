<?php
namespace Lcobucci\ActionMapper2\Errors;

abstract class HttpException extends \Exception
{
    /**
     *
     * @return int
     */
    abstract public function getStatusCode();
}
