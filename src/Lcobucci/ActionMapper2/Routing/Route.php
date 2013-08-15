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
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface Route
{
    /**
     * @param Application $application
     */
    public function setApplication(Application $application);

    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @param Response $response
     */
    public function setResponse(Response $response);
}
