<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

/**
 * Forbidden can be use to say that the requested resource is no longer
 * available at the server and no forwarding address is known.
 *
 * @author Luis Henrique Mulinari <mulinari@gmail.com>
 */
class GoneException extends HttpException
{
    /**
     * Returns the HTTP status code
     *
     * @return int
     */
    public function getStatusCode()
    {
        return 410;
    }
}
