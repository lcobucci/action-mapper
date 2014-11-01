<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing;

use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;

/**
 * The creator of route metadata
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class RouteDefinitionCreator
{
    /**
     * A regex to a parameter
     *
     * @var string
     */
    const PARAM = '/\([a-zA-Z0-9\_]{1,}\)/';

    /**
     * A regex to a URI
     *
     * @var string
     */
    const URI_PATTERN = '/^\/(\(?[a-zA-Z0-9-_\*\.%]{1,}\)?\/?)*$/';

    /**
     * A regex to a slash
     *
     * @var string
     */
    const BAR = '/\//';

    /**
     * A regex to a dot
     *
     * @var string
     */
    const DOT = '/\./';

    /**
     * A regex to a wildcard
     *
     * @var string
     */
    const WILDCARD1 = '/%/';

    /**
     * A regex to another wildcard
     *
     * @var string
     */
    const WILDCARD2 = '/\*/';

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var RouteHandlerContainer
     */
    private $handlerContainer;

    /**
     * @param Reader $annotaionReader
     * @param RouteHandlerContainer $handlerContainer
     */
    public function __construct(
        Reader $annotaionReader,
        RouteHandlerContainer $handlerContainer
    ) {
        $this->annotationReader = $annotaionReader;
        $this->handlerContainer = $handlerContainer;
    }

    /**
     * Creates a new route definition
     *
     * @param string $pattern
     * @param Route|Filter|\Closure|string $handler
     * @param array $httpMethods
     *
     * @return RouteDefinition
     */
    public function create(
        $pattern,
        $handler,
        array $httpMethods = null
    ) {
        $pattern = static::preparePattern($pattern);

        $route = new RouteDefinition(
            $pattern,
            static::createRegex($pattern),
            $handler,
            $httpMethods
        );

        $route->setAnnotationReader($this->annotationReader);
        $route->setHandlerContainer($this->handlerContainer);

        return $route;
    }

    /**
     * Creates a regex based on pattern
     *
     * @param string $pattern
     * @param bool $addEnd
     * @return string
     */
    public static function createRegex($pattern, $addEnd = true)
    {
        $regex = '/^';
        $regex .= preg_replace(
            array(
                static::PARAM,
                static::BAR,
                static::DOT,
                static::WILDCARD1,
                static::WILDCARD2
            ),
            array('([^/]+)', '\/', '\.', '.*', '.*'),
            $pattern
        );

        $regex = str_replace('\\/.*', '(\\/.*)?', $regex);
        $regex .= $addEnd ? '$/' : '/';

        return $regex;
    }

    /**
     * Removes the trailling slash and simplifies the params on the pattern
     *
     * @param string $pattern
     * @return boolean
     *
     * @throws InvalidArgumentException
     */
    public static function preparePattern($pattern)
    {
        if (preg_match(static::URI_PATTERN, $pattern) == 0) {
            throw new InvalidArgumentException('Pattern "' . $pattern . '" is invalid');
        }

        if ($pattern != '/') {
            $pattern = rtrim($pattern, '/');
        }

        return preg_replace(static::PARAM, '(x)', $pattern);
    }
}
