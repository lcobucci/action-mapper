<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper;

use Lcobucci\ActionMapper\DependencyInjection\Container as ActionMapperContainer;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Lcobucci\ActionMapper\Routing\RouteManager;
use Lcobucci\ActionMapper\Errors\ErrorHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The application is main resource of this library, it is capable of handling
 * and dispatching requests
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Application
{
    /**
     * The route manager
     *
     * @var RouteManager
     */
    protected $routeManager;

    /**
     * The error handler
     *
     * @var ErrorHandler
     */
    protected $errorHandler;

    /**
     * The dependency container
     *
     * @var ContainerInterface
     */
    private $dependencyContainer;

    /**
     * The HTTP Request
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP Response
     *
     * @var Response
     */
    protected $response;

    /**
     * Class constructor
     *
     * @param RouteManager $routeManager
     * @param ErrorHandler $errorHandler
     * @param ContainerInterface $dependencyContainer
     */
    public function __construct(
        RouteManager $routeManager,
        ErrorHandler $errorHandler,
        ContainerInterface $dependencyContainer = null
    ) {
        $this->routeManager = $routeManager;
        $this->errorHandler = $errorHandler;

        if ($dependencyContainer !== null) {
            $this->setDependencyContainer($dependencyContainer);
        }
    }

    /**
     * Configures the dependency container, injecting the application if need
     *
     * @param ContainerInterface $dependencyContainer
     */
    public function setDependencyContainer(ContainerInterface $dependencyContainer)
    {
        if ($dependencyContainer instanceof ActionMapperContainer) {
            $dependencyContainer->setApplication($this);
        }

        $this->dependencyContainer = $dependencyContainer;
    }

    /**
     * Returns the dependency container
     *
     * @return ContainerInterface
     */
    public function getDependencyContainer()
    {
        return $this->dependencyContainer;
    }

    /**
     * Returns the route manager
     *
     * @return RouteManager
     */
    public function getRouteManager()
    {
        return $this->routeManager;
    }

    /**
     * Returns the HTTP request (creating if not configured)
     *
     * @return Request
     */
    public function getRequest()
    {
        if ($this->request === null) {
            $this->request = Request::createFromGlobals();
        }

        return $this->request;
    }

    /**
     * Returns the HTTP response (creating if not configured)
     *
     * @return Response
     */
    public function getResponse()
    {
        if ($this->response === null) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * Starts the session using the given name
     *
     * @param string $name
     */
    public function startSession($name = null)
    {
        $this->setDefaultSession($name);
        $this->getSession()->start();
    }

    /**
     * Configure the session handler with the native session (only when not
     * configured)
     *
     * @param string $name
     */
    protected function setDefaultSession($name = null)
    {
        if ($this->getRequest()->hasSession()) {
            return ;
        }

        $options = array();

        if ($name !== null) {
            $options['name'] = $name;
        }

        $this->setSession(new Session(new NativeSessionStorage($options)));
    }

    /**
     * Configure the session handler (only when not configured)
     *
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        if (!$this->getRequest()->hasSession()) {
            $this->getRequest()->setSession($session);
        }
    }

    /**
     * Returns the session handler
     *
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->getRequest()->getSession();
    }

    /**
     * Redirect to given URI (using HTTP headers)
     *
     * @param string $url
     * @param int $statusCode
     */
    public function redirect($url, $statusCode = 302)
    {
        //FIXME
        if (strpos($url, 'http') !== 0) {
            $url = $this->getRequest()->getBasePath() . $url;
        }

        $this->getResponse()->redirect($url, $statusCode);
        $this->sendResponse();
    }

    /**
     * Forward the engine to given path
     *
     * @param string $path
     * @param boolean $interrupt
     */
    public function forward($path, $interrupt = false)
    {
        //FIXME
        try {
            $request = $this->getRequest();
            $previousPath = $request->getRequestedPath();

            $request->setRequestedPath($path);
            $this->routeManager->process($this);
            $request->setRequestedPath($previousPath);
        } catch (\Exception $error) {
            $this->errorHandler->handle($error);
        }

        if (isset($error) || $interrupt) {
            $this->sendResponse();
        }
    }

    /**
     * Executes the application
     */
    public function run()
    {
        $this->errorHandler->setRequest($this->getRequest());
        $this->errorHandler->setResponse($this->getResponse());

        try {
            ob_start();
            $this->routeManager->process($this);
            ob_end_clean();
        } catch (\Exception $error) {
            $this->errorHandler->handle($error);
        }

        $this->sendResponse();
    }

    /**
     * Sends the response to the browser
     */
    protected function sendResponse()
    {
        $response = $this->getResponse();

        $response->prepare($this->getRequest());
        $response->send();
    }
}
