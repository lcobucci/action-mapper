<?php
namespace Lcobucci\ActionMapper2\Config;

use Doctrine\Common\Cache\Cache;
use Lcobucci\DependencyInjection\XmlContainerBuilder;
use Lcobucci\DependencyInjection\ContainerBuilder;
use Lcobucci\ActionMapper2\Errors\DefaultHandler;
use Lcobucci\ActionMapper2\Errors\ErrorHandler;
use Lcobucci\ActionMapper2\Application;
use Symfony\Component\DependencyInjection\ContainerInterface;
use InvalidArgumentException;

class ApplicationBuilder
{
    /**
     * @var string
     */
    const DEFAULT_BASE_CONTAINER = '\Lcobucci\ActionMapper2\DependencyInjection\Container';

    /**
     * @var RoutesBuilder
     */
    protected $routesBuilder;

    /**
     * @var ContainerBuilder
     */
    protected $containerBuilder;

    /**
     * @param string $routesConfig
     * @param string $containerConfig
     * @param ErrorHandler $errorHandler
     * @param string $cacheDir
     * @param string $containerBaseClass
     * @param Cache|string $applicationCache
     * @return Application
     */
    public static function build(
        $routesConfig,
        $containerConfig = null,
        ErrorHandler $errorHandler = null,
        $cacheDir = null,
        $containerBaseClass = null,
        $applicationCache = null
    ) {
        $builder = new static(
            new RoutesBuilder(),
            new XmlContainerBuilder(
                $containerBaseClass ?: static::DEFAULT_BASE_CONTAINER,
                $cacheDir
            )
        );

        $dependencyContainer = null;
        if ($containerConfig !== null) {
            $dependencyContainer = $builder->containerBuilder
                                           ->getContainer(realpath($containerConfig));
        }

        $builder->configureCache($dependencyContainer, $applicationCache);

        $routeManager = $builder->routesBuilder
                                ->build(realpath($routesConfig));

        return new Application(
            $routeManager,
            $errorHandler ?: new DefaultHandler(),
            $dependencyContainer
        );
    }

    /**
     * @param RoutesBuilder $routesBuilder
     * @param ContainerBuilder $containerBuilder
     */
    public function __construct(
        RoutesBuilder $routesBuilder,
        ContainerBuilder $containerBuilder
    ) {
        $this->routesBuilder = $routesBuilder;
        $this->containerBuilder = $containerBuilder;
    }

    /**
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
            $this->routesBuilder->setCache($applicationCache);

            return ;
        }

        if ($container && is_string($applicationCache)) {
            $this->routesBuilder->setCache($container->get($applicationCache));

            return ;
        }

        throw new InvalidArgumentException(
            'Application cache must be an instance of Cache or an existing '
            . 'service on container'
        );
    }
}
