<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use InvalidArgumentException;
use Lcobucci\ActionMapper2\Application;
use Lcobucci\ActionMapper2\DependencyInjection\Container;
use Lcobucci\ActionMapper2\DependencyInjection\ContainerConfig;
use Lcobucci\ActionMapper2\Errors\DefaultHandler;
use Lcobucci\ActionMapper2\Errors\ErrorHandler;
use Lcobucci\DependencyInjection\Builders\XmlBuilder;

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
     * Builds a ready-to-use application with given application
     *
     * @param string $routesConfig
     * @param ContainerConfig $containerConfig
     * @param ErrorHandler $errorHandler
     *
     * @return Application
     */
    public static function build(
        $routesConfig,
        ContainerConfig $containerConfig = null,
        ErrorHandler $errorHandler = null
    ) {
        $dependencyContainer = self::createContainer($containerConfig);

        $builder = new static(
            $dependencyContainer->get('app.routes.builder')
        );

        return $builder->create(
            $routesConfig,
            $dependencyContainer,
            $errorHandler
        );
    }

    /**
     * @param ContainerConfig $containerConfig
     *
     * @return Container
     */
    private static function createContainer(ContainerConfig $containerConfig = null)
    {
        if ($containerConfig === null) {
            return new Container();
        }

        $builder = new XmlBuilder();
        return $builder->getContainer($containerConfig);
    }

    /**
     * Class constructor
     *
     * @param RouteBuilder $routeBuilder
     */
    public function __construct(RouteBuilder $routeBuilder)
    {
        $this->routeBuilder = $routeBuilder;
    }

    /**
     * @param string $routesConfig
     * @param Container $dependencyContainer
     * @param ErrorHandler $errorHandler
     *
     * @return Application
     */
    public function create(
        $routesConfig,
        Container $dependencyContainer,
        ErrorHandler $errorHandler = null
    ) {
        return new Application(
            $this->routeBuilder->build(realpath($routesConfig)),
            $errorHandler ?: new DefaultHandler(),
            $dependencyContainer
        );
    }
}
