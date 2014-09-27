<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Config;

use Lcobucci\ActionMapper\DependencyInjection\ContainerConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Lcobucci\DependencyInjection\XmlContainerBuilder;
use Lcobucci\DependencyInjection\ContainerBuilder;
use Lcobucci\ActionMapper\Errors\DefaultHandler;
use Lcobucci\ActionMapper\Errors\ErrorHandler;
use Lcobucci\ActionMapper\Application;
use Doctrine\Common\Cache\Cache;
use InvalidArgumentException;

/**
 * The application builder is a factory to create applications using
 * configuration files
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ApplicationBuilder
{
    /**
     * The route builder
     *
     * @var RouteBuilder
     */
    protected $routeBuilder;

    /**
     * The dependency injection builder
     *
     * @var ContainerBuilder
     */
    protected $containerBuilder;

    /**
     * Builds a ready-to-use application with given application
     *
     * @param string $routesConfig
     * @param ContainerConfig $containerConfig
     * @param ErrorHandler $errorHandler
     * @param Cache|string $applicationCache
     * @return Application
     */
    public static function build(
        $routesConfig,
        ContainerConfig $containerConfig = null,
        ErrorHandler $errorHandler = null,
        $applicationCache = null
    ) {
        $builder = new static(
            new RouteBuilder(),
            new XmlContainerBuilder()
        );

        $dependencyContainer = null;

        if ($containerConfig !== null) {
            $dependencyContainer = $builder->containerBuilder->getContainer($containerConfig);
        }

        $builder->configureCache($dependencyContainer, $applicationCache);

        $routeManager = $builder->routeBuilder
                                ->build(realpath($routesConfig));

        return new Application(
            $routeManager,
            $errorHandler ?: new DefaultHandler(),
            $dependencyContainer
        );
    }

    /**
     * Class constructor
     *
     * @param RouteBuilder $routeBuilder
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(
        RouteBuilder $routeBuilder,
        ContainerBuilder $containerBuilder
    ) {
        $this->routeBuilder = $routeBuilder;
        $this->containerBuilder = $containerBuilder;
    }

    /**
     * Configure the cache provider for route builder
     *
     * @param ContainerInterface $container
     * @param string $applicationCache
     * @throws InvalidArgumentException
     */
    protected function configureCache(
        ContainerInterface $container = null,
        $applicationCache = null
    ) {
        if ($applicationCache === null) {
            return ;
        }

        if ($applicationCache instanceof Cache) {
            $this->routeBuilder->setCache($applicationCache);

            return ;
        }

        if ($container && is_string($applicationCache)) {
            $this->routeBuilder->setCache($container->get($applicationCache));

            return ;
        }

        throw new InvalidArgumentException(
            'Application cache must be an instance of Cache or an existing '
            . 'service on container'
        );
    }
}
