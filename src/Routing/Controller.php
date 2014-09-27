<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Routing;

use Lcobucci\ActionMapper\Application;
use BadMethodCallException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
     * @see \Lcobucci\ActionMapper\Routing\Route::setRequest()
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Configures the response
     *
     * @see \Lcobucci\ActionMapper\Routing\Route::setResponse()
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Configures the application
     *
     * @see \Lcobucci\ActionMapper\Routing\Route::setApplication()
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
     * @throws BadMethodCallException
     */
    public function get($serviceId)
    {
        if ($this->application->getDependencyContainer() === null) {
            throw new BadMethodCallException(
                'The dependency container must be defined'
            );
        }

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
