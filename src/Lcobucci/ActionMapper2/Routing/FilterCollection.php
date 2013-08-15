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
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class FilterCollection
{
    /**
     * @var string
     */
    const FILTER_CLASS = '\Lcobucci\ActionMapper2\Routing\Filter';

    /**
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
     * @param string $pattern
     * @param boolean $before
     * @param Filter|\Closure|string $handler
     * @throws InvalidArgumentException
     */
    public function append($pattern, $before, $handler)
    {
        if (isset($this->filters[(int) $before])) {
            $filterChain = $this->filters[(int) $before];
        } else {
            $filterChain = array();
        }

        if ($handler instanceof \Closure || $this->isValidHandler($handler)) {
            $filterChain[] = RouteDefinitionCreator::create($pattern, $handler);
            $this->filters[(int) $before] = $filterChain;

            return ;
        }

        throw new InvalidArgumentException(
            'You must pass a closure or a class that extends the '
            . static::FILTER_CLASS . ' class'
        );
    }

    /**
     * @param object|string $handler
     * @return boolean
     */
    protected function isValidHandler($handler)
    {
        return is_subclass_of($handler, static::FILTER_CLASS);
    }

    /**
     * @param string $path
     * @param bool $before
     * @return RouteDefinition
     */
    public function findFiltersFor($path, $before = true)
    {
        if (!isset($this->filters[(int) $before])) {
            return array();
        }

        $filterChain = array();

        foreach ($this->filters[(int) $before] as $config) {
            if ($config->match($path)) {
                $filterChain[] = $config;
            }
        }

        return $filterChain;
    }
}
