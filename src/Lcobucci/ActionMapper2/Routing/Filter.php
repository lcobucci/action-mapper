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
use BadMethodCallException;

/**
 * Base filter class, it allows to create routines that can be processed before
 * or after the request
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class Filter
{
    /**
     * The application
     *
     * @var Application
     */
    protected $application;

    /**
     * The HTTP request
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP response
     *
     * @var Response
     */
    protected $response;

    /**
     * Configures the application
     *
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Configures the request
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Configures the response
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Returns a service from the dependency injection container
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
     * Process the filter's job
     */
    abstract public function process();
}
