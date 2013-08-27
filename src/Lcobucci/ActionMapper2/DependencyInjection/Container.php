<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\DependencyInjection;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Lcobucci\ActionMapper2\Application;

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
}
