<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http\Server;

use Lcobucci\ActionMapper\Http\Exception;

/**
 * An internal server error means that the server encountered an unexpected
 * condition which prevented it from fulfilling the request.
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class InternalServerError extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 500;
    }
}
