<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Doctrine\Common\Annotations\Reader;
use Lcobucci\ActionMapper2\DependencyInjection\Container;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteHandlerContainer
{
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var Container
     */
    private $diContainer;

    /**
     * @var array
     */
    protected $handlers;

    /**
     * @param Reader $annotationReader
     * @param Container $diContainer
     * @param array $handlers
     */
    public function __construct(
        Reader $annotationReader,
        Container $diContainer,
        array $handlers = array()
    ) {
        $this->annotationReader = $annotationReader;
        $this->diContainer = $diContainer;
        $this->handlers = $handlers;
    }

    /**
     * @param string $className
     * @return mixed
     */
    public function get($className)
    {
        if (!isset($this->handlers[$className])) {
            $this->handlers[$className] = new $className;
        }

        return $this->handlers[$className];
    }
}
