<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Doctrine\Common\Annotations\Reader;
use Lcobucci\ActionMapper2\DependencyInjection\Container;
use ReflectionClass;
use ReflectionMethod;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteHandlerContainer
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var Container
     */
    private $diContainer;

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @param Reader $annotationReader
     * @param Container $diContainer
     * @param array $handlers
     */
    public function __construct(
        Reader $annotationReader,
        Container $diContainer,
        array $handlers = array()
    ) {
        $this->annotationReader = $annotationReader;
        $this->diContainer = $diContainer;
        $this->handlers = $handlers;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        if (!isset($this->handlers[$className])) {
            $this->handlers[$className] = $this->loadHandler($className);
        }

        return $this->handlers[$className];
    }

    /**
     * @param string $className
     *
     * @return object
     */
    private function loadHandler($className)
    {
        $class = new ReflectionClass($className);
        $handler = $this->createHandler($class);

        foreach ($class->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            $this->injectServices($method, $handler);
        }

        return $handler;
    }

    /**
     * @param ReflectionClass $class
     * @return object
     */
    private function createHandler(ReflectionClass $class)
    {
        $arguments = array();

        if ($constructor = $class->getConstructor()) {
            $arguments = $this->getServices($constructor);
        }

        return $class->newInstanceArgs($arguments);
    }

    /**
     * @param ReflectionMethod $method
     * @param object $handler
     */
    private function injectServices(ReflectionMethod $method, $handler)
    {
        $services = $this->getServices($method);

        if (!empty($services)) {
            $method->invokeArgs($handler, $services);
        }
    }

    /**
     * @param ReflectionMethod $method
     *
     * @return array
     */
    private function getServices(ReflectionMethod $method)
    {
        $services = array();
        $annotation = $this->annotationReader->getMethodAnnotation(
            $method,
            'Lcobucci\ActionMapper2\Routing\Annotation\Inject'
        );

        if ($annotation === null) {
            return $services;
        }

        foreach ($annotation->getServices() as $serviceId) {
            $services[] = $this->diContainer->get($serviceId);
        }

        return $services;
    }
}
