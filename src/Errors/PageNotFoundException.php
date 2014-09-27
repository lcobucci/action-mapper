<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

/**
 * Page not found happens when the server has not found anything matching the
 * Request-URI
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class PageNotFoundException extends HttpException
{
    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 404;
    }
}
