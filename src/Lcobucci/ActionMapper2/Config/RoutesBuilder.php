<?php
namespace Lcobucci\ActionMapper2\Config;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Lcobucci\ActionMapper2\Routing\RouteCollection;
use Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use Lcobucci\ActionMapper2\Routing\RouteManager;
use Doctrine\Common\Cache\Cache;
use stdClass;

class RoutesBuilder
{
    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var RouteLoader
     */
    protected $routeLoader;

    /**
     * @param RouteLoader $routeLoader
     */
    public function __construct(RouteLoader $routeLoader = null)
    {
        $this->routeLoader = $routeLoader ?: new XmlRoutesLoader();
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $fileName
     * @return RouteManager
     */
    public function build($fileName)
    {
        return $this->createManager($this->getMetadata($fileName));
    }

    /**
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
                    $filter->before
                );
            }
        }

        return $manager;
    }

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
     * @param string $key
     * @param stdClass $metadata
     */
    protected function saveToCache($key, stdClass $metadata)
    {
        $this->cache->save($key, $metadata);
        $this->cache->save($key . '.time', time());
    }

    /**
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
     * @param string $fileName
     * @return stdClass
     */
    protected function createMetadata($fileName)
    {
        return $this->routeLoader->load($fileName);
    }
}
