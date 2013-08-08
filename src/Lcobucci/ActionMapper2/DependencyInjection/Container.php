<?php
namespace Lcobucci\ActionMapper2\DependencyInjection;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Lcobucci\ActionMapper2\Application;

class Container extends \Symfony\Component\DependencyInjection\Container
{
    /**
     * @var Application
     */
    protected $application;

    /**
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
