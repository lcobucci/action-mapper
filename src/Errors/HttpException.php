<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

/**
 * Base class for HTTP error responses
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class HttpException extends \RuntimeException
{
    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    abstract public function getStatusCode();
}
