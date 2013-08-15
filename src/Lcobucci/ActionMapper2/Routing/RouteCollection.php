<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Lcobucci\ActionMapper2\Errors\PageNotFoundException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use ReflectionClass;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteCollection
{
    /**
     * @var array
     */
    private $routes;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * Class constructor
     *
     * @param Reader $annotationReader
     */
    public function __construct(Reader $annotationReader = null)
    {
        $this->annotationReader = $annotationReader ?: new AnnotationReader();
        $this->routes = array();
    }

    /**
     * @param string $pattern
     * @param Route|\Closure|string $handler
     * @throws InvalidArgumentException
     */
    public function append($pattern, $handler)
    {
        if (isset($this->routes[$pattern])) {
            throw new InvalidArgumentException(
                'Pattern already mapped'
            );
        }

        if (!$handler instanceof \Closure
            && !$this->isValidHandler($handler)) {
            throw new InvalidArgumentException(
                'You must pass a closure or a class that implements'
                . ' \Lcobucci\ActionMapper2\Routing\Route interface'
            );
        }

        $this->routes[$pattern] = RouteDefinitionCreator::create(
            $pattern,
            $handler,
            $this->annotationReader
        );

        $this->sortByKeyLength();
    }

    /**
     * Sorts the collection by the patterns length
     */
    protected function sortByKeyLength()
    {
        uksort(
            $this->routes,
            function ($one, $other) {
                $oneLength = strlen($one);
                $otherLength = strlen($other);

                return $oneLength > $otherLength
                       ? 1
                       : ($oneLength == $otherLength ? 0 : -1);
            }
        );

        $this->routes = array_reverse($this->routes, true);
    }

    /**
     * @param object|string $handler
     * @return boolean
     */
    protected function isValidHandler($handler)
    {
        if (is_string($handler) && strpos($handler, '::') !== false) {
            $handler = substr($handler, 0, strpos($handler, '::'));
        }

        if (is_object($handler)
            || (is_string($handler) && class_exists($handler))) {
            $reflection = new ReflectionClass($handler);

            return $reflection->implementsInterface(
                '\Lcobucci\ActionMapper2\Routing\Route'
            );
        }

        return false;
    }

    /**
     * @param string $path
     * @return RouteDefinition
     * @throws PageNotFoundException
     */
    public function findRouteFor($path)
    {
        foreach ($this->routes as $route) {
            if ($route->match($path)) {
                return $route;
            }
        }

        throw new PageNotFoundException('No route for the requested path');
    }
}
