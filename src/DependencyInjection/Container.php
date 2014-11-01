<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\DependencyInjection;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Lcobucci\ActionMapper2\Application;
use Lcobucci\ActionMapper2\Config\Loader\Xml;
use Lcobucci\ActionMapper2\Config\RouteBuilder;
use Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use Lcobucci\ActionMapper2\Routing\RouteHandlerContainer;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * This container provides the application session service
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Container extends \Symfony\Component\DependencyInjection\Container
{
    /**
     * The application
     *
     * @var Application
     */
    protected $application;

    /**
     * Configures the application
     *
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Gets the 'session' service.
     *
     * @return SessionInterface
     */
    protected function getSessionService()
    {
        return $this->services['session'] = $this->application->getSession();
    }

    /**
     * @return \Doctrine\Common\Cache\ArrayCache
     */
    protected function getApp_CacheService()
    {
        return $this->services['app.cache'] = new \Doctrine\Common\Cache\ArrayCache();
    }

    /**
     * @return \Lcobucci\ActionMapper2\Config\RouteBuilder
     */
    protected function getApp_Routes_BuilderService()
    {
        return $this->services['app.routes.builder'] = new RouteBuilder(
            $this->get('app.routes.loader'),
            $this->get('app.routes.definition.creator'),
            $this->get('app.cache')
        );
    }

    /**
     * @return \Lcobucci\ActionMapper2\Config\Loader\Xml
     */
    protected function getApp_Routes_LoaderService()
    {
        return $this->services['app.routes.loader'] = new Xml();
    }

    /**
     * @return \Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator
     */
    protected function getApp_Routes_Definition_CreatorService()
    {
        return $this->services['app.routes.definition.creator'] = new RouteDefinitionCreator(
            $this->get('app.annotations.reader'),
            $this->get('app.routes.handler.container')
        );
    }

    /**
     * @return \Lcobucci\ActionMapper2\Routing\RouteHandlerContainer
     */
    protected function getApp_Routes_Handler_ContainerService()
    {
        return $this->services['app.routes.handler.container'] = new RouteHandlerContainer(
            $this->get('app.annotations.reader'),
            $this
        );
    }

    /**
     * @return \Doctrine\Common\Annotations\CachedReader
     */
    protected function getApp_Annotations_ReaderService()
    {
        return $this->services['app.annotations.reader'] = new CachedReader(
            new AnnotationReader(),
            $this->get('app.cache')
        );
    }
}
