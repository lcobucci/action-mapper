<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

/**
 * The method specified in the Request-Line is not allowed for the resource
 * identified by the Request-URI.
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class MethodNotAllowedException extends HttpException
{
    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 405;
    }
}
