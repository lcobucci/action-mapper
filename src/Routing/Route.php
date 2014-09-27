<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Routing;

use Lcobucci\ActionMapper\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defines the basic methods that a route must have
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface Route
{
    /**
     * Configures the application
     *
     * @param Application $application
     */
    public function setApplication(Application $application);

    /**
     * Configures the request
     *
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * Configures the response
     *
     * @param Response $response
     */
    public function setResponse(Response $response);
}
