<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Lcobucci\ActionMapper2\Application;

/**
 * The route manager process filters and routes for a path
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteManager
{
    /**
     * The route list
     *
     * @var RouteCollection
     */
    private $routes;

    /**
     * The filter list
     *
     * @var FilterCollection
     */
    private $filters;

    /**
     * Class constructor
     *
     * @param RouteCollection $routes
     * @param FilterCollection $filters
     */
    public function __construct(
        RouteCollection $routes = null,
        FilterCollection $filters = null
    ) {
        $this->routes = $routes ?: new RouteCollection();
        $this->filters = $filters ?: new FilterCollection();
    }

    /**
     * Appends a new route
     *
     * @param string $pattern
     * @param string|object $handler
     */
    public function addRoute($pattern, $handler)
    {
        $this->routes->append($pattern, $handler);
    }

    /**
     * Appends a new filter
     *
     * @param string $pattern
     * @param string|object $hander
     * @param boolean $before
     */
    public function addFilter($pattern, $handler, $before = true)
    {
        $this->filters->append($pattern, $before, $handler);
    }

    /**
     * Process the current request
     *
     * @param Application $application
     */
    public function process(Application $application)
    {
        $this->processFilters($application);
        $this->processRoute($application);
        $this->processFilters($application, false);
    }

    /**
     * Process the filters of current request
     *
     * @param Application $application
     * @param boolean $before
     */
    protected function processFilters(Application $application, $before = true)
    {
        $filters = $this->filters->findFiltersFor(
            $application->getRequest()->getRequestedPath(),
            $before
        );

        foreach ($filters as $filter) {
            $filter->process($application);
        }
    }

    /**
     * Process the route of current request
     *
     * @param Application $application
     */
    protected function processRoute(Application $application)
    {
        $route = $this->routes->findRouteFor(
            $application->getRequest()->getRequestedPath()
        );

        $route->process($application);
    }
}
