<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;
use Lcobucci\ActionMapper2\Application;

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
