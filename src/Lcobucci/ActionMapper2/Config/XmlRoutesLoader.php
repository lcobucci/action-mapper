<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use InvalidArgumentException;
use SimpleXMLElement;
use DOMDocument;
use stdClass;

/**
 * Configuration loader for XML files
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class XmlRoutesLoader implements RouteLoader
{
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

        return $this->createMetadata($config);
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

        $config = new SimpleXMLElement($file, null, true);

        //FIXME if XML file already has a prefix the loader don't work

        foreach ($this->getNamespaces() as $prefix => $uri) {
            $config->registerXPathNamespace($prefix, $uri);
        }

        return $config;
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
        return __DIR__ . '/schema/routing.xsd';
    }

    /**
     * Return the list of namespaces that must be registered before validation
     *
     * @return array
     */
    protected function getNamespaces()
    {
        return array(
            'routing' => 'http://lcobucci.com/action-mapper/schema'
        );
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
            $handler = (string) $route['class'];

            if (isset($route['method'])) {
                $handler .= '::' . $route['method'];
            }

            $metadata->routes[] = (object) array(
                'pattern' => (string) $route['pattern'],
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
            $metadata->filters[] = (object) array(
                'pattern' => (string) $filter['pattern'],
                'handler' => (string) $filter['class'],
                'before' => !isset($filter['before']) || $filter['before'] == 'true'
            );
        }
    }
}
