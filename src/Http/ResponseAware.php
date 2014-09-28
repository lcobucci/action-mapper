<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http;

use Symfony\Component\HttpFoundation\Response;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface ResponseAware
{
    /**
     * @param Response $response
     */
    public function setResponse(Response $response);
}
