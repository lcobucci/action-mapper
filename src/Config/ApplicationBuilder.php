<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use Lcobucci\ActionMapper2\Application;
use Lcobucci\ActionMapper2\Errors\DefaultHandler;
use Lcobucci\ActionMapper2\Errors\ErrorHandler;
use Lcobucci\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @param ContainerBuilder $containerBuilder
     * @param ErrorHandler $errorHandler
     *
     * @return Application
     */
    public static function build(
        $routesConfig,
        ContainerBuilder $containerBuilder = null,
        ErrorHandler $errorHandler = null
    ) {
        $dependencyContainer = self::createContainer($containerBuilder);

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
     * @param ContainerBuilder $containerBuilder
     *
     * @return ContainerInterface
     */
    private static function createContainer(ContainerBuilder $containerBuilder = null)
    {
        $containerBuilder = $containerBuilder ?: new ContainerBuilder();

        return $containerBuilder->addFile(__DIR__ . '/../../config/services.xml')
                                ->getContainer();
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
     * @param ContainerInterface $dependencyContainer
     * @param ErrorHandler $errorHandler
     *
     * @return Application
     */
    public function create(
        $routesConfig,
        ContainerInterface $dependencyContainer,
        ErrorHandler $errorHandler = null
    ) {
        return new Application(
            $this->routeBuilder->build(realpath($routesConfig)),
            $errorHandler ?: new DefaultHandler(),
            $dependencyContainer
        );
    }
}
