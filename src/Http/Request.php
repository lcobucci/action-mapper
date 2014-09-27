<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http;

/**
 * Abstraction for HTTP request
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Request extends \Symfony\Component\HttpFoundation\Request
{
    /**
     * The requested path
     *
     * @var string
     */
    protected $requestedPath;

    /**
     * Configures the requested path
     *
     * @param string $requestedPath
     */
    public function setRequestedPath($requestedPath)
    {
        $this->requestedPath = $requestedPath;
    }

    /**
     * Returns the requested path
     *
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
