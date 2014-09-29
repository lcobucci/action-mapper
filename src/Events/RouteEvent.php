<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteEvent extends Event
{
    const PROCESS = 'app.router.process';

    const REDIRECT = 'app.router.redirect';

    /**
     * @var string
     */
    private $path;

    /**
     * @param Request $request
     * @param Response $response
     * @param string $path
     */
    public function __construct(Request $request, Response $response, $path = null)
    {
        parent::__construct($request, $response);

        $this->path = $path ?: $request->getPathInfo();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
