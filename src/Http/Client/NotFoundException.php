<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http\Client;

use Lcobucci\ActionMapper\Http\Exception;

/**
 * Resource not found happens when the server has not found anything matching the
 * Request-URI
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class NotFoundException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 404;
    }
}
