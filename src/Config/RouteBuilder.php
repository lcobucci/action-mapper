<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use Doctrine\Common\Cache\Cache;
use Lcobucci\ActionMapper2\Routing\FilterCollection;
use Lcobucci\ActionMapper2\Routing\RouteCollection;
use Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use Lcobucci\ActionMapper2\Routing\RouteManager;
use stdClass;

/**
 * The route builder create routes from the parsed configuration
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteBuilder
{
    /**
     * The configuration loader
     *
     * @var RouteLoader
     */
    private $loader;

    /**
     * @var RouteDefinitionCreator
     */
    private $definitionCreator;

    /**
     * The cache provider to be used by annotation reader
     *
     * @var Cache
     */
    private $cache;

    /**
     * @param RouteLoader $loader
     * @param RouteDefinitionCreator $definitionCreator
     * @param Cache $cache
     */
    public function __construct(
        RouteLoader $loader,
        RouteDefinitionCreator $definitionCreator,
        Cache $cache
    ) {
        $this->loader = $loader;
        $this->definitionCreator = $definitionCreator;
        $this->cache = $cache;
    }

    /**
     * Build the route manager from the configuration file
     *
     * @param string $fileName
     *
     * @return RouteManager
     */
    public function build($fileName)
    {
        return $this->configureManager(
            new RouteManager(
                new RouteCollection($this->definitionCreator),
                new FilterCollection($this->definitionCreator)
            ),
            $this->getMetadata($fileName)
        );
    }

    /**
     * Appends the routes and filters to route manager
     *
     * @param RouteManager $manager
     * @param stdClass $metadata
     *
     * @return RouteManager
     */
    private function configureManager(RouteManager $manager, stdClass $metadata)
    {
        foreach ($metadata->routes as $route) {
            $manager->addRoute($route->pattern, $route->handler);
        }

        if (isset($metadata->filters)) {
            foreach ($metadata->filters as $filter) {
                $manager->addFilter(
                    $filter->pattern,
                    $filter->handler,
                    $filter->before,
                    $filter->httpMethods
                );
            }
        }

        return $manager;
    }

    /**
     * Retrieve the configuration data
     *
     * @param string $fileName
     *
     * @return stdClass
     */
    private function getMetadata($fileName)
    {
        $key = md5($fileName);

        if ($cachedData = $this->loadFromCache($key, $fileName)) {
            return $cachedData;
        }

        $metadata = $this->loader->load($fileName);
        $this->saveToCache($key, $metadata);

        return $metadata;
    }

    /**
     * Save the configuration data into the cache provider
     *
     * @param string $key
     * @param stdClass $metadata
     */
    private function saveToCache($key, stdClass $metadata)
    {
        $this->cache->save($key, $metadata);
        $this->cache->save($key . '.time', time());
    }

    /**
     * Load the configuration data from cache (when cache provider is configured)
     *
     * @param string $key
     * @param string $fileName
     * @return stdClass
     */
    private function loadFromCache($key, $fileName)
    {
        $metadata = $this->cache->fetch($key);

        if ($metadata && $this->cache->fetch($key . '.time') > filemtime($fileName)) {
            return $metadata;
        }

        return null;
    }
}
