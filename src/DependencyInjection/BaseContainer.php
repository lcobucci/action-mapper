<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection;

use Lcobucci\ActionMapper\Http\RequestInjector;
use Lcobucci\ActionMapper\Http\ResponseInjector;
use Symfony\Component\DependencyInjection\Container as SymfonyContainer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class BaseContainer extends SymfonyContainer implements Container
{
    use RequestInjector, ResponseInjector;

    /**
     * @return SessionInterface
     */
    protected function getApp_SessionService()
    {
        return $this->services['app.session'] = $this->request->getSession();
    }

    /**
     * @return Request
     */
    protected function getApp_RequestService()
    {
        return $this->services['app.request'] = $this->request;
    }

    /**
     * @return Response
     */
    protected function getApp_ResponseService()
    {
        return $this->services['app.response'] = $this->response;
    }
}
