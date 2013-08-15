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
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Controller implements Route
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @see \Lcobucci\ActionMapper2\Routing\Route::setRequest()
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @see \Lcobucci\ActionMapper2\Routing\Route::setResponse()
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @see \Lcobucci\ActionMapper2\Routing\Route::setApplication()
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
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
     * @param string $path
     * @param boolean $interrupt
     */
    public function forward($path, $interrupt = false)
    {
        $this->application->forward($path, $interrupt);
    }

    /**
     * @param string $url
     */
    public function redirect($url)
    {
        $this->application->redirect($url);
    }
}
