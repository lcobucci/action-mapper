<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use InvalidArgumentException;
use Lcobucci\ActionMapper2\Errors\PageNotFoundException;

/**
 * A collection of all application routes
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteCollection
{
    /**
     * @var string
     */
    const ROUTE_INTERFACE = '\Lcobucci\ActionMapper2\Routing\Route';

    /**
     * @var RouteDefinitionCreator
     */
    private $definitionCreator;

    /**
     * The list of routes
     *
     * @var array
     */
    private $routes;

    /**
     * Sort controll for route list
     *
     * @var boolean
     */
    private $sorted;

    /**
     * @param RouteDefinitionCreator $definitionCreator
     */
    public function __construct(RouteDefinitionCreator $definitionCreator)
    {
        $this->definitionCreator = $definitionCreator;
        $this->routes = array();
        $this->sorted = false;
    }

    /**
     * Append a route for given pattern
     *
     * @param string $pattern
     * @param Route|\Closure|string $handler
     *
     * @throws InvalidArgumentException
     */
    public function append($pattern, $handler)
    {
        if (isset($this->routes[$pattern])) {
            throw new InvalidArgumentException(
                'Pattern already mapped'
            );
        }

        if (!$this->isValidHandler($handler)) {
            throw new InvalidArgumentException(
                'You must pass a closure or a class that implements'
                . ' ' . static::ROUTE_INTERFACE . ' interface'
            );
        }

        $this->sorted = false;

        $this->routes[$pattern] = $this->definitionCreator->create($pattern, $handler);
    }

    /**
     * Sorts the collection by the patterns length
     */
    protected function sortByKeyLength()
    {
        if ($this->sorted) {
            return ;
        }

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
        $this->sorted = true;
    }

    /**
     * Verifies if handler is valid
     *
     * @param object|string $handler
     * @return boolean
     */
    protected function isValidHandler($handler)
    {
        if ($handler instanceof \Closure) {
            return true;
        }

        if (is_string($handler) && strpos($handler, '::') !== false) {
            $handler = substr($handler, 0, strpos($handler, '::'));
        }

        if (is_object($handler) || (is_string($handler) && class_exists($handler))) {
            return is_subclass_of($handler, static::ROUTE_INTERFACE);
        }

        return false;
    }

    /**
     * Locates a route for given path
     *
     * @param string $path
     * @return RouteDefinition
     *
     * @throws PageNotFoundException
     */
    public function findRouteFor($path)
    {
        $this->sortByKeyLength();

        foreach ($this->routes as $route) {
            if ($route->match($path)) {
                return $route;
            }
        }

        throw new PageNotFoundException('No route for the requested path');
    }
}
