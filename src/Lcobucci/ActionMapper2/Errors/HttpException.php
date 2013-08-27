<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

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
