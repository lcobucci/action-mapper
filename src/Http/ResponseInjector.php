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
trait ResponseInjector
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @param Response $request
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
