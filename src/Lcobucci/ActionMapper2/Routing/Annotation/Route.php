<?php
namespace Lcobucci\ActionMapper2\Routing\Annotation;

use \Lcobucci\ActionMapper2\Routing\RouteDefinition;
use \Lcobucci\ActionMapper2\Routing\RouteDefinitionCreator;
use \Lcobucci\ActionMapper2\Errors\BadRequestException;
use \Lcobucci\ActionMapper2\Http\Request;
use \InvalidArgumentException;

/**
 * @Annotation
 * @Target({"METHOD"})
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Route
{
    /**
     * @var string
     */
    public $pattern = '/';

    /**
     * @var array
     */
    public $requirements = array();

    /**
     * @var array
     */
    public $methods = array('GET', 'POST', 'PUT', 'DELETE');

    /**
     * @var array
     */
    public $contentType = array();

    /**
     * @var array
     */
    private $matchedArgs;

    /**
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
     * @param string $pattern
     */
    protected function setPattern(&$pattern)
    {
        $this->pattern = RouteDefinitionCreator::preparePattern($pattern);
    }

    /**
     * @param array $contentType
     */
    protected function setContentType(array $contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * @param \Lcobucci\ActionMapper2\Routing\RouteDefinition $route
     * @param \Lcobucci\ActionMapper2\Http\Request $request
     * @return boolean
     */
    public function match(RouteDefinition $route, Request $request)
    {
        if (!$this->validatePattern($route, $request)
            || !$this->validateMethod($request)
            || !$this->validateContentType($request)
            || !$this->validateRequirements($request)) {
            return false;
        }

        return true;
    }

    /**
     * @return array
     */
    public function getMatchedArgs()
    {
        return $this->matchedArgs;
    }

    /**
     * @param \Lcobucci\ActionMapper2\Routing\RouteDefinition $route
     * @param \Lcobucci\ActionMapper2\Http\Request $request
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
     * @param \Lcobucci\ActionMapper2\Routing\RouteDefinition $route
     * @param \Lcobucci\ActionMapper2\Http\Request $request
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
     * @param \Lcobucci\ActionMapper2\Http\Request $request
     * @return boolean
     */
    protected function validateMethod(Request $request)
    {
        return in_array($request->getMethod(), $this->methods);
    }

    /**
     * @param \Lcobucci\ActionMapper2\Http\Request $request
     * @return boolean
     */
    protected function validateContentType(Request $request)
    {
        if (!isset($this->contentType[0])) {
            return true;
        }

        $acceptableTypes = $request->getAcceptableContentTypes();

        if (!isset($acceptableTypes[1]) && $acceptableTypes[0] == '*/*') {
            return true;
        }

        foreach ($acceptableTypes as $contentType) {
            if (in_array($contentType, $this->contentType)) {
                return true;
            }
        }
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
     * @param string $expression
     * @param string $value
     */
    protected function validateRequirement($expression, $value)
    {
        if (strlen($expression) == 0) {
            return true;
        }

        return preg_match('/^' . $expression . '$/', $value);
    }
}
