<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

/**
 * The request could not be completed due to a conflict with the current state of the resource.
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ConflictException extends HttpException
{
    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 409;
    }
}
