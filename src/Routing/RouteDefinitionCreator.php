<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Routing;

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
     * The defaul definition class
     *
     * @var string
     */
    const DEFINITION_CLASS = '\Lcobucci\ActionMapper\Routing\RouteDefinition';

    /**
     * The base definition class to be used
     *
     * @var string
     */
    protected static $baseClass;

    /**
     * @var RouteHandlerContainer
     */
    protected static $handlerContainer;

    /**
     * @param RouteHandlerContainer $handlerContainer
     */
    public static function setHandlerContainer(RouteHandlerContainer $handlerContainer)
    {
        static::$handlerContainer = $handlerContainer;
    }

    /**
     * @return RouteHandlerContainer
     */
    public static function getHandlerContainer()
    {
        if (static::$handlerContainer === null) {
            static::setHandlerContainer(new RouteHandlerContainer());
        }

        return static::$handlerContainer;
    }

    /**
     * Configures the base definition class
     *
     * @param string $baseClass
     * @throws InvalidArgumentException
     */
    public static function setBaseClass($baseClass)
    {
        $baseClass = (string) $baseClass;

        if (is_subclass_of($baseClass, static::DEFINITION_CLASS)) {
            static::$baseClass = $baseClass;
            return ;
        }

        throw new InvalidArgumentException(
            'Route definition must be instance of '
            . '\Lcobucci\ActionMapper\Routing\RouteDefinition'
        );
    }

    /**
     * Creates a new route definition
     *
     * @param string $pattern
     * @param Route|Filter|\Closure|string $handler
     * @param Reader $annotationReader
     * @param array $httpMethods
     * @return RouteDefinition
     */
    public static function create(
        $pattern,
        $handler,
        Reader $annotationReader = null,
        array $httpMethods = null
    ) {
        $baseClass = static::$baseClass ?: static::DEFINITION_CLASS;
        $pattern = static::preparePattern($pattern);

        $route = new $baseClass(
            $pattern,
            static::createRegex($pattern),
            $handler,
            $httpMethods
        );

        if ($annotationReader) {
            $route->setAnnotationReader($annotationReader);
        }

        $route->setHandlerContainer(static::getHandlerContainer());

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
     * @throws InvalidArgumentException
     */
    public static function preparePattern($pattern)
    {
        if (preg_match(static::URI_PATTERN, $pattern) == 0) {
            throw new InvalidArgumentException(
                'Pattern "' . $pattern . '" is invalid'
            );
        }

        if ($pattern != '/') {
            $pattern = rtrim($pattern, '/');
        }

        return preg_replace(
            static::PARAM,
            '(x)',
            $pattern
        );
    }
}
