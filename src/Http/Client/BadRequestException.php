<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http\Client;

use Lcobucci\ActionMapper\Http\Exception;

/**
 * A bad request is a HTTP request that could not be understood by the server
 * due to malformed syntax
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class BadRequestException extends Exception
{
    /**
     * {@inheritdoc}
     */
    public function getStatusCode()
    {
        return 400;
    }
}