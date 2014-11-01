<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Lcobucci\ActionMapper2\Errors\PageNotFoundException;
use Lcobucci\ActionMapper2\Application;
use Doctrine\Common\Annotations\Reader;
use ReflectionClass;
use ReflectionMethod;
use RuntimeException;

/**
 * Metadata of a route
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteDefinition
{
    /**
     * The configured pattern
     *
     * @var string
     */
    protected $pattern;

    /**
     * The created regex based on pattern
     *
     * @var string
     */
    protected $regex;

    /**
     * The handler to be called
     *
     * @var string
     */
    protected $handler;

    /**
     * The matched arguments
     *
     * @var array
     */
    protected $matchedArgs;

    /**
     * @var array
     */
    protected $httpMethods;

    /**
     * The annotation reader
     *
     * @var Reader
     */
    protected $annotationReader;

    /**
     * @var RouteHandlerContainer
     */
    protected $handlerContainer;

    /**
     * Class constructor
     *
     * @param string $pattern
     * @param string $regex
     * @param string $handler
     * @param array $httpMethods
     */
    public function __construct(
        $pattern,
        $regex,
        $handler,
        array $httpMethods = null
    ) {
        $this->pattern = $pattern;
        $this->regex = $regex;
        $this->handler = $handler;
        $this->httpMethods = $httpMethods;
    }

    /**
     * Configures the annotation reader
     *
     * @param Reader $annotationReader
     */
    public function setAnnotationReader(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    /**
     * @param RouteHandlerContainer $handlerContainer
     */
    public function setHandlerContainer(RouteHandlerContainer $handlerContainer)
    {
        $this->handlerContainer = $handlerContainer;
    }

    /**
     * Configures the pattern
     *
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * Returns if given path matches with current regex and http method (if passed)
     *
     * @param string $path
     * @param string $requestedMethod
     *
     * @return boolean
     */
    public function match($path, $requestedMethod = null)
    {
        if (!$this->methodMatch($requestedMethod)) {
            return false;
        }

        $numParams = substr_count($this->regex, '([^\/]+)');

        if (preg_match($this->regex, $path, $this->matchedArgs)) {
            array_shift($this->matchedArgs);

            while (count($this->matchedArgs) > $numParams) {
                array_pop($this->matchedArgs);
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $requestedMethod
     *
     * @return boolean
     */
    protected function methodMatch($requestedMethod)
    {
        if ($requestedMethod === null || $this->httpMethods === null) {
            return true;
        }

        return in_array($requestedMethod, $this->httpMethods);
    }

    /**
     * Appends the handler return to application response
     *
     * @param Application $application
     */
    public function process(Application $application)
    {
        if ($content = $this->getContent($application)) {
            $application->getResponse()->appendContent((string) $content);
        }
    }

    /**
     * Calls the handler returning its content
     *
     * @param Application $application
     *
     * @return string
     */
    protected function getContent(Application $application)
    {
        $callback = $this->getHandlerCallback();
        $callback[0]->setRequest($application->getRequest());
        $callback[0]->setResponse($application->getResponse());
        $callback[0]->setApplication($application);

        if (!isset($callback[1])) {
            return $this->parseAnnotation($callback[0], $application);
        }

        return call_user_func_array($callback, $this->matchedArgs);
    }

    /**
     * Returns the handler
     *
     * @return Route|Filter
     */
    protected function getHandlerCallback()
    {
        $callback = explode('::', $this->handler);
        $callback[0] = $this->handlerContainer->get($callback[0]);

        if ($callback[0] instanceof Filter) {
            $callback[1] = 'process';
        }

        return $callback;
    }

    /**
     * Parses the class annotations
     *
     * @param Route $handler
     * @param Application $application
     *
     * @return mixed
     *
     * @throws RuntimeException
     * @throws PageNotFoundException
     */
    protected function parseAnnotation(Route $handler, Application $application)
    {
        $class = new ReflectionClass($handler);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $annotation = $this->annotationReader->getMethodAnnotation(
                $method,
                '\Lcobucci\ActionMapper2\Routing\Annotation\Route'
            );

            if ($annotation && $annotation->match($this, $application->getRequest())) {
                return $method->invokeArgs(
                    $handler,
                    array_merge($this->matchedArgs, (array) $annotation->getMatchedArgs())
                );
            }
        }

        throw new PageNotFoundException('No route for the requested path');
    }
}
