<?php
namespace Lcobucci\ActionMapper2\Config;

use Lcobucci\DependencyInjection\XmlContainerBuilder;
use \Lcobucci\DependencyInjection\ContainerBuilder;
use \Lcobucci\ActionMapper2\Errors\DefaultHandler;
use \Lcobucci\ActionMapper2\Errors\ErrorHandler;
use \Lcobucci\ActionMapper2\Application;

class ApplicationBuilder
{
    /**
     * @var string
     */
    const DEFAULT_BASE_CONTAINER = '\Lcobucci\ActionMapper2\DependencyInjection\Container';

    /**
     * @var \Lcobucci\ActionMapper2\Config\RoutesBuilder
     */
    protected $routesBuilder;

    /**
     * @var \Lcobucci\DependencyInjection\ContainerBuilder
     */
    protected $containerBuilder;

    /**
     * @var string
     */
    protected static $cacheDir;

    /**
     * @var string
     */
    protected static $containerBaseClass;

    /**
     * @param string $routesConfig
     * @param string $containerConfig
     * @param string $cacheDir
     * @param \Lcobucci\ActionMapper2\Errors\ErrorHandler $errorHandler
     * @return \Lcobucci\ActionMapper2\Application
     */
    public static function build(
        $routesConfig,
        $containerConfig = null,
        ErrorHandler $errorHandler = null,
        $cacheDir = null,
        $containerBaseClass = null
    ) {
        static::$cacheDir = $cacheDir;
        static::$containerBaseClass = $containerBaseClass ?: static::DEFAULT_BASE_CONTAINER;

        $builder = new static();
        $routeManager = $builder->routesBuilder
                                ->build(realpath($routesConfig));

        $dependencyContainer = null;
        if ($containerConfig !== null) {
            $dependencyContainer = $builder->containerBuilder
                                           ->getContainer(realpath($containerConfig));
        }

        return new Application(
            $routeManager,
            $errorHandler ?: new DefaultHandler(),
            $dependencyContainer
        );
    }

    /**
     * @param \Lcobucci\ActionMapper2\Config\RoutesBuilder $routesBuilder
     * @param \Lcobucci\Common\DependencyInjection\ContainerBuilder $containerBuilder
     */
    public function __construct(
        RoutesBuilder $routesBuilder = null,
        ContainerBuilder $containerBuilder = null
    ) {
        if ($routesBuilder === null) {
            $routesBuilder = new RoutesBuilder(static::$cacheDir);
        }

        if ($containerBuilder === null) {
            $containerBuilder = new XmlContainerBuilder(
                static::$containerBaseClass,
                static::$cacheDir
            );
        }

        $this->routesBuilder = $routesBuilder;
        $this->containerBuilder = $containerBuilder;
    }
}
