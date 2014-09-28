<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper;

use Exception;
use Lcobucci\ActionMapper\DependencyInjection\Container;
use Lcobucci\ActionMapper\Events\ApplicationEvent;
use Lcobucci\ActionMapper\Events\ExceptionEvent;
use Lcobucci\ActionMapper\Http\RequestAware;
use Lcobucci\ActionMapper\Http\ResponseAware;
use Lcobucci\ActionMapper\Http\RequestInjector;
use Lcobucci\ActionMapper\Http\ResponseInjector;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The application is main resource of this library, it is capable of handling
 * and dispatching requests
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Application implements RequestAware, ResponseAware
{
    use RequestInjector, ResponseInjector;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     * @param Request $request
     * @param Response $response
     */
    public function __construct(
        Container $container,
        Request $request = null,
        Response $response = null
    ) {
        $this->request = $request ?: $this->createRequest();
        $this->response = $response ?: $this->createResponse();

        $this->setContainer($container);
    }

    /**
     * @param Container $container
     */
    private function setContainer(Container $container)
    {
        $this->container = $container;
        $this->container->setRequest($this->request);
        $this->container->setResponse($this->response);
    }

    /**
     * @return Request
     */
    protected function createRequest()
    {
        return Request::createFromGlobals();
    }

    /**
     * @return Response
     */
    protected function createResponse()
    {
        return new Response();
    }

    /**
     * @param SessionInterface $session
     */
    public function startSession(SessionInterface $session)
    {
        $this->request->setSession($session);

        $session->start();
    }

    public function run()
    {
        /* @var $dispatcher EventDispatcherInterface */
        $dispatcher = $this->container->get('app.eventDispatcher');

        try {
            $this->start($dispatcher);
        } catch (Exception $exception) {
            $this->handleException($dispatcher, $exception);
        } finally {
            $this->terminate($dispatcher);
        }
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    private function start(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            ApplicationEvent::START,
            new ApplicationEvent($this->request, $this->response)
        );
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     * @param Exception $exception
     */
    private function handleException(EventDispatcherInterface $dispatcher, Exception $exception)
    {
        $dispatcher->dispatch(
            ExceptionEvent::EXCEPTION,
            new ExceptionEvent($this->request, $this->response, $exception)
        );
    }

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    private function terminate(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->dispatch(
            ApplicationEvent::TERMINATE,
            new ApplicationEvent($this->request, $this->response)
        );
    }
}
