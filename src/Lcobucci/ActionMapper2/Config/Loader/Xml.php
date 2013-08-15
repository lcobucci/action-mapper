<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config\Loader;

use Lcobucci\ActionMapper2\Config\RouteLoader;
use InvalidArgumentException;
use SimpleXMLElement;
use DOMDocument;
use stdClass;

/**
 * Configuration loader for XML files
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Xml implements RouteLoader
{
    /**
     * The default namespace to be used
     *
     * @var string
     */
    const XML_NAMESPACE = 'http://lcobucci.com/action-mapper/schema';

    /**
     * Load the file and returns the configuration data
     *
     * @param string $file
     * @return stdClass
     */
    public function load($file)
    {
        $config = $this->loadFile($file);
        $this->validate($config);

        return $this->createMetadata($config->children(static::XML_NAMESPACE));
    }

    /**
     * Creates an object to be parsed from the given file
     *
     * @param string $file
     * @return SimpleXMLElement
     * @throws InvalidArgumentException
     */
    protected function loadFile($file)
    {
        if (!is_readable($file)) {
            throw new InvalidArgumentException(
                'File "' . $file . '" is not readable'
            );
        }

        return new SimpleXMLElement($file, null, true);
    }

    /**
     * Validate the configuration file using the XSD
     *
     * @param SimpleXMLElement $config
     * @throws InvalidArgumentException
     */
    protected function validate(SimpleXMLElement $config)
    {
        $current = libxml_use_internal_errors(true);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $dom->loadXML($config->asXML());
        $valid = $dom->schemaValidate($this->getXsdPath());

        if (!$valid) {
            throw new InvalidArgumentException(
                implode("\n", $this->getXmlErrors())
            );
        }

        libxml_use_internal_errors($current);
    }

    /**
     * Returns an array of XML errors.
     *
     * @return array
     */
    protected function getXmlErrors()
    {
        $errors = array();

        foreach (libxml_get_errors() as $error) {
            $errors[] = sprintf(
                '[%s %s] %s (in %s - line %d, column %d)',
                LIBXML_ERR_WARNING == $error->level ? 'WARNING' : 'ERROR',
                $error->code,
                trim($error->message),
                $error->file ? $error->file : 'n/a',
                $error->line,
                $error->column
            );
        }

        libxml_clear_errors();

        return $errors;
    }

    /**
     * Returns the path of schema file
     *
     * @return string
     */
    protected function getXsdPath()
    {
        return __DIR__ . '/../schema/routing.xsd';
    }

    /**
     * Creates the configuration data from the XML element
     *
     * @param SimpleXMLElement $config
     * @return stdClass
     */
    protected function createMetadata(SimpleXMLElement $config)
    {
        $metadata = new stdClass();
        $metadata->routes = array();

        if (isset($config->definitionBaseClass)) {
            $metadata->definitionBaseClass = (string) $config->definitionBaseClass;
        }

        $this->parseRoutes($config, $metadata);
        $this->parseFilters($config, $metadata);

        return $metadata;
    }

    /**
     * Parses the routes from the XML object
     *
     * @param SimpleXMLElement $config
     * @param stdClass $metadata
     */
    protected function parseRoutes(SimpleXMLElement $config, stdClass $metadata)
    {
        foreach ($config->routes->route as $route) {
            $attributes = $route->attributes();
            $handler = (string) $attributes->class;

            if (isset($attributes->method)) {
                $handler .= '::' . $attributes->method;
            }

            $metadata->routes[] = (object) array(
                'pattern' => (string) $attributes->pattern,
                'handler' => $handler
            );
        }
    }

    /**
     * Parses the filters from the XML object
     *
     * @param SimpleXMLElement $config
     * @param stdClass $metadata
     */
    protected function parseFilters(SimpleXMLElement $config, stdClass $metadata)
    {
        if (!isset($config->filters)) {
            return;
        }

        $metadata->filters = array();

        foreach ($config->filters->filter as $filter) {
            $attributes = $filter->attributes();

            $metadata->filters[] = (object) array(
                'pattern' => (string) $attributes->pattern,
                'handler' => (string) $attributes->class,
                'before' => !isset($attributes->before) || $attributes->before == 'true'
            );
        }
    }
}
