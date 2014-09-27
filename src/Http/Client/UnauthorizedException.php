<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http\Client;

use Lcobucci\ActionMapper\Http\Exception;

/**
 * Unauthorized response happens when the request requires user authentication
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class UnauthorizedException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 401;
    }
}
