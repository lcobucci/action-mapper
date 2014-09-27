<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use InvalidArgumentException;

/**
 * A collection of filters waiting to be called
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class FilterCollection
{
    /**
     * The filter base class
     *
     * @var string
     */
    const FILTER_CLASS = '\Lcobucci\ActionMapper2\Routing\Filter';

    /**
     * The list of filters
     *
     * @var array
     */
    private $filters;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->filters = array();
    }

    /**
     * Append a new filter
     *
     * @param string $pattern
     * @param boolean $before
     * @param Filter|\Closure|string $handler
     * @param array $httpMethods
     * @throws InvalidArgumentException
     */
    public function append($pattern, $before, $handler, array $httpMethods = null)
    {
        if (isset($this->filters[(int) $before])) {
            $filterChain = $this->filters[(int) $before];
        } else {
            $filterChain = array();
        }

        if ($handler instanceof \Closure || $this->isValidHandler($handler)) {
            $filterChain[] = RouteDefinitionCreator::create($pattern, $handler, null, $httpMethods);
            $this->filters[(int) $before] = $filterChain;

            return ;
        }

        throw new InvalidArgumentException(
            'You must pass a closure or a class that extends the '
            . static::FILTER_CLASS . ' class'
        );
    }

    /**
     * Validates if handler is a subclass of the base filter
     *
     * @param object|string $handler
     * @return boolean
     */
    protected function isValidHandler($handler)
    {
        return is_subclass_of($handler, static::FILTER_CLASS);
    }

    /**
     * Locates all filters for given path
     *
     * @param string $path
     * @param string $requestedMethod
     * @param bool $before
     * @return RouteDefinition
     */
    public function findFiltersFor($path, $requestedMethod, $before = true)
    {
        if (!isset($this->filters[(int) $before])) {
            return array();
        }

        $filterChain = array();

        foreach ($this->filters[(int) $before] as $config) {
            if ($config->match($path, $requestedMethod)) {
                $filterChain[] = $config;
            }
        }

        return $filterChain;
    }
}
