<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http\Client;

use Lcobucci\ActionMapper\Http\Exception;

/**
 * The method specified in the Request-Line is not allowed for the resource
 * identified by the Request-URI.
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class MethodNotAllowedException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 405;
    }
}
