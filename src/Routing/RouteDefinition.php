<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Routing;

use Lcobucci\ActionMapper\Errors\PageNotFoundException;
use Lcobucci\ActionMapper\Application;
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
     * @var Route|Filter|\Closure|string
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
     * @param Route|Filter|\Closure|string $handler
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
        $content = $this->getContent($application);

        if ($content) {
            $application->getResponse()->appendContent((string) $content);
        }
    }

    /**
     * Calls the handler returning its content
     *
     * @param Application $application
     * @return string
     */
    protected function getContent(Application $application)
    {
        if ($this->handler instanceof \Closure) {
            return call_user_func_array($this->handler, $this->matchedArgs);
        }

        $method = null;

        $handler = $this->getHandler($method);
        $handler->setRequest($application->getRequest());
        $handler->setResponse($application->getResponse());
        $handler->setApplication($application);

        if ($handler instanceof Filter) {
            return call_user_func_array(
                array($handler, 'process'),
                $this->matchedArgs
            );
        }

        if ($method !== null) {
            $this->validateCustomAnnotations(
                $application,
                new ReflectionMethod($handler, $method)
            );

            return call_user_func_array(
                array($handler, $method),
                $this->matchedArgs
            );
        }

        return $this->parseAnnotation($handler, $application);
    }

    /**
     * Returns the handler
     *
     * @param string $method
     * @return Route|Filter
     */
    protected function getHandler(&$method)
    {
        if (is_string($this->handler)) {
            if (strpos($this->handler, '::') !== false) {
                list($class, $method) = explode('::', $this->handler);
            } else {
                $class = $this->handler;
            }

            return $this->handlerContainer->get($class);
        }

        return $this->handler;
    }

    /**
     * Parses the class annotations
     *
     * @param Route $handler
     * @param Application $application
     * @return mixed
     * @throws RuntimeException
     * @throws PageNotFoundException
     */
    protected function parseAnnotation(Route $handler, Application $application)
    {
        if ($this->annotationReader === null) {
            throw new RuntimeException('Annotation parser is not setted');
        }

        $class = new ReflectionClass($handler);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $annotation = $this->annotationReader->getMethodAnnotation(
                $method,
                '\Lcobucci\ActionMapper\Routing\Annotation\Route'
            );

            if ($annotation
                && $annotation->match($this, $application->getRequest())) {
                $this->validateCustomAnnotations($application, $method);

                return $method->invokeArgs(
                    $handler,
                    array_merge(
                        $this->matchedArgs,
                        (array) $annotation->getMatchedArgs()
                    )
                );
            }
        }

        throw new PageNotFoundException('No route for the requested path');
    }

    /**
     * Validate custom annotations
     *
     * @param Application $application
     * @param ReflectionMethod $method
     */
    protected function validateCustomAnnotations(
        Application $application,
        ReflectionMethod $method
    ) {
        // Override if needed
    }
}
