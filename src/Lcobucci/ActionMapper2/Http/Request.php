<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Http;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * @var string
     */
    protected $requestedPath;

    /**
     * @param string $requestedPath
     */
    public function setRequestedPath($requestedPath)
    {
        $this->requestedPath = $requestedPath;
    }

    /**
     * @return string
     */
    public function getRequestedPath()
    {
        $path = $this->requestedPath ?: $this->getPathInfo();

        if ($path != '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
