<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing\Annotation;

use Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use Lcobucci\ActionMapper2\Routing\RouteDefinition;
use Lcobucci\ActionMapper2\Http\Request;
use InvalidArgumentException;

/**
 * The annotation to be used on controllers methods and map the routes
 *
 * @Annotation
 * @Target({"METHOD"})
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Route
{
    /**
     * The URI pattern to handle
     *
     * @var string
     */
    public $pattern = '/';

    /**
     * The list of requirements that must be applied on each segment
     *
     * @var array
     */
    public $requirements = array();

    /**
     * The list of HTTP methods to handle
     *
     * @var array
     */
    public $methods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * The list of content types to handle
     *
     * @var array
     */
    public $contentType = array();

    /**
     * The matched URI segments
     *
     * @var array
     */
    private $matchedArgs;

    /**
     * Class constructor
     *
     * @param array $options
     */
    public function __construct(array $options = array())
    {
        if (isset($options['value'])) {
            $this->setPattern($options['value']);
        }

        if (isset($options['pattern'])) {
            $this->setPattern($options['pattern']);
        }

        if (isset($options['requirements'])) {
            $this->setRequirements($options['requirements']);
        }

        if (isset($options['methods'])) {
            $this->setMethods($options['methods']);
        }

        if (isset($options['contentType'])) {
            $this->setContentType($options['contentType']);
        }
    }

    /**
     * Configures the requirement list
     *
     * @param array $requirements
     * @throws InvalidArgumentException
     */
    protected function setRequirements(array $requirements)
    {
        if (!is_array($requirements)) {
            throw new InvalidArgumentException(
                'Route requirements must be an array'
            );
        }

        $this->requirements = $requirements;
    }

    /**
     * Configures the HTTP methods
     *
     * @param array $methods
     * @throws InvalidArgumentException
     */
    protected function setMethods(array $methods)
    {
        if (!is_array($methods)) {
            throw new InvalidArgumentException(
                'Route methods must be an array'
            );
        }

        $validMethods = array(
            'GET',
            'POST',
            'PUT',
            'DELETE',
            'OPTIONS',
            'HEAD',
            'TRACE',
            'CONNECT'
        );

        foreach ($methods as $method) {
            if (!in_array($method, $validMethods)) {
                throw new InvalidArgumentException(
                    '"' . $method . '" is not a valid route method '
                    . '(only ' . implode(',', $validMethods) . ' are allowed)'
                );
            }
        }

        $this->methods = $methods;
    }

    /**
     * Configures the pattern
     *
     * @param string $pattern
     */
    protected function setPattern(&$pattern)
    {
        $this->pattern = RouteDefinitionCreator::preparePattern($pattern);
    }

    /**
     * Configures the list of acceptable content-types
     *
     * @param array $contentType
     */
    protected function setContentType(array $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Check if the route matches with request
     *
     * @param RouteDefinition $route
     * @param Request $request
     * @return boolean
     */
    public function match(RouteDefinition $route, Request $request)
    {
        if (!$this->validatePattern($route, $request)
            || !$this->validateMethod($request)
            || !$this->validateAccept($request)
            || !$this->validateRequirements($request)) {
            return false;
        }

        return true;
    }

    /**
     * Return the matched items
     *
     * @return array
     */
    public function getMatchedArgs()
    {
        return $this->matchedArgs;
    }

    /**
     * Validate if request URI matches with pattern
     *
     * @param RouteDefinition $route
     * @param Request $request
     * @return boolean
     */
    protected function validatePattern(RouteDefinition $route, Request $request)
    {
        $path = $this->getRequestedPath($route, $request);
        $regex = RouteDefinitionCreator::createRegex($this->pattern);

        if (preg_match($regex, $path, $this->matchedArgs)) {
            array_shift($this->matchedArgs);

            return true;
        }

        return false;
    }

    /**
     * Returns the requested path
     *
     * @param RouteDefinition $route
     * @param Request $request
     * @return string
     */
    protected function getRequestedPath(RouteDefinition $route, Request $request)
    {
        $pattern = rtrim($route->getPattern(), '/*');
        $regex = RouteDefinitionCreator::createRegex($pattern, false);
        $path = preg_replace($regex, '', $request->getRequestedPath());

        if (substr($path, 0, 1) != '/') {
            $path = '/' . $path;
        }

        if ($path != '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }

    /**
     * Validates if requested method is handled by annotation
     *
     * @param Request $request
     * @return boolean
     */
    protected function validateMethod(Request $request)
    {
        return in_array($request->getMethod(), $this->methods);
    }

    /**
     * Validates if request content-type is handled by annotation
     *
     * @param Request $request
     * @return boolean
     */
    protected function validateAccept(Request $request)
    {
        if (!isset($this->contentType[0])) {
            return true;
        }

        $acceptableTypes = $request->getAcceptableContentTypes();

        if (!isset($acceptableTypes[0])
            || (!isset($acceptableTypes[1]) && $acceptableTypes[0] == '*/*')) {
            return true;
        }

        foreach ($acceptableTypes as $requested) {
            if ($this->validateContentType($requested)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $requested
     * @return boolean
     */
    protected function validateContentType($requested)
    {
        foreach ($this->contentType as $required) {
            if ($requested === $required) {
                return true;
            }

            list($requestedType, $requestedContent) = explode('/', $requested);
            list($requiredType, $requiredContent) = explode('/', $required);

            if ($requestedType == $requiredType
                && ($requestedContent == '*' || $requiredContent == '*' || $requiredContent == $requestedContent)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validates the requirements
     */
    protected function validateRequirements()
    {
        if (!isset($this->requirements[0]) || !is_array($this->matchedArgs)) {
            return true;
        }

        foreach ($this->matchedArgs as $index => $value) {
            if (isset($this->requirements[$index])
                && !$this->validateRequirement($this->requirements[$index], $value)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if value matches with requirement expression
     *
     * @param string $expression
     * @param string $value
     * @return bool
     */
    protected function validateRequirement($expression, $value)
    {
        if (strlen($expression) == 0) {
            return true;
        }

        return preg_match('/^' . $expression . '$/', $value);
    }
}
