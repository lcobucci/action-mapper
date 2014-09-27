<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use Lcobucci\ActionMapper2\Routing\RouteCollection;
use Doctrine\Common\Annotations\AnnotationReader;
use Lcobucci\ActionMapper2\Routing\RouteManager;
use Lcobucci\ActionMapper2\Config\Loader\Xml;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\Cache;
use stdClass;

/**
 * The route builder create routes from the parsed configuration
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteBuilder
{
    /**
     * The cache provider to be used by annotation reader
     *
     * @var Cache
     */
    protected $cache;

    /**
     * The configuration loader
     *
     * @var RouteLoader
     */
    protected $routeLoader;

    /**
     * Class constructor
     *
     * @param RouteLoader $routeLoader
     */
    public function __construct(RouteLoader $routeLoader = null)
    {
        $this->routeLoader = $routeLoader ?: new Xml();
    }

    /**
     * Configures the cache provider
     *
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Build the route manager from the configuration file
     *
     * @param string $fileName
     * @return RouteManager
     */
    public function build($fileName)
    {
        return $this->createManager($this->getMetadata($fileName));
    }

    /**
     * Create the route manager from configuration data
     *
     * @param stdClass $metadata
     * @return RouteManager
     */
    protected function createManager(stdClass $metadata)
    {
        if (isset($metadata->definitionBaseClass)) {
            RouteDefinitionCreator::setBaseClass($metadata->definitionBaseClass);
        }

        $routes = new RouteCollection(
            $this->cache ? new CachedReader(new AnnotationReader(), $this->cache) : null
        );

        return $this->configureManager(new RouteManager($routes), $metadata);
    }

    /**
     * Appends the routes and filters to route manager
     *
     * @param RouteManager $manager
     * @param stdClass $metadata
     * @return RouteManager
     */
    protected function configureManager(RouteManager $manager, stdClass $metadata)
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
     * @return stdClass
     */
    protected function getMetadata($fileName)
    {
        $key = md5($fileName);

        $cachedData = $this->loadFromCache($key, $fileName);
        $metadata = $cachedData ?: $this->createMetadata($fileName);

        if ($this->cache && !$cachedData) {
            $this->saveToCache($key, $metadata);
        }

        return $metadata;
    }

    /**
     * Save the configuration data into the cache provider
     *
     * @param string $key
     * @param stdClass $metadata
     */
    protected function saveToCache($key, stdClass $metadata)
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
    protected function loadFromCache($key, $fileName)
    {
        if (!$this->cache) {
            return null;
        }

        if (($metadata = $this->cache->fetch($key))
            && $this->cache->fetch($key . '.time') > filemtime($fileName)) {
            return $metadata;
        }

        return null;
    }

    /**
     * Retrieve the configuration data using the loader
     *
     * @param string $fileName
     * @return stdClass
     */
    protected function createMetadata($fileName)
    {
        return $this->routeLoader->load($fileName);
    }
}
