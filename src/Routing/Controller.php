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
 * Base class of controllers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Controller implements Route
{
    /**
     * The current request
     *
     * @var Request
     */
    protected $request;

    /**
     * The current response
     *
     * @var Response
     */
    protected $response;

    /**
     * The application
     *
     * @var Application
     */
    protected $application;

    /**
     * Configures the request
     *
     * @see \Lcobucci\ActionMapper2\Routing\Route::setRequest()
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Configures the response
     *
     * @see \Lcobucci\ActionMapper2\Routing\Route::setResponse()
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Configures the application
     *
     * @see \Lcobucci\ActionMapper2\Routing\Route::setApplication()
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Get a service from dependency injection container
     *
     * @param string $serviceId
     * @return mixed
     *
     * @deprecated Use explicity injection
     */
    public function get($serviceId)
    {
        return $this->application->getDependencyContainer()->get($serviceId);
    }

    /**
     * Forward the current request to another path
     *
     * @param string $path
     * @param boolean $interrupt
     */
    public function forward($path, $interrupt = false)
    {
        $this->application->forward($path, $interrupt);
    }

    /**
     * Redirect to another path
     *
     * @param string $url
     */
    public function redirect($url)
    {
        $this->application->redirect($url);
    }
}
