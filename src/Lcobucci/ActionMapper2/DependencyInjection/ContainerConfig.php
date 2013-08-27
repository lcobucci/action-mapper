<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\DependencyInjection;

/**
 * This is the basic configuration to build the dependency injection container
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ContainerConfig
{
    /**
     * The default dependency container
     *
     * @var string
     */
    const DEFAULT_CONTAINER = '\Lcobucci\ActionMapper2\DependencyInjection\Container';

    /**
     * The configuration file
     *
     * @var string
     */
    protected $file;

    /**
     * The directory to save the dump file
     *
     * @var string
     */
    protected $dumpDirectory;

    /**
     * The container base class
     *
     * @var string
     */
    protected $baseClass;

    /**
     * The container default parameters
     *
     * @var array
     */
    protected $defaultParameters;

    /**
     * Class constructor
     *
     * @param string $file
     * @param string $dumpDirectory
     * @param string $baseClass
     * @param array $defaultParameters
     */
    public function __construct(
        $file,
        $dumpDirectory = null,
        $baseClass = self::DEFAULT_CONTAINER,
        array $defaultParameters = array()
    ) {
        $this->file = realpath($file);
        $this->dumpDirectory = $dumpDirectory;
        $this->baseClass = $baseClass;
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * Returns the absolute path of configuration file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Returns the directory to save the dump file
     *
     * @return string
     */
    public function getDumpDirectory()
    {
        return $this->dumpDirectory;
    }

    /**
     * Returns the container's base class
     *
     * @return string
     */
    public function getBaseClass()
    {
        return $this->baseClass;
    }

    /**
     * Returns the default parameters
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return $this->defaultParameters;
    }

    /**
     * Returns which class will be used
     *
     * @param ContainerConfig $config
     * @return string
     */
    public static function getClass(ContainerConfig $config = null)
    {
        if ($config) {
            return $config->getBaseClass();
        }

        return static::DEFAULT_CONTAINER;
    }

    /**
     * Returns which directory should be used to save the dump file
     *
     * @param ContainerConfig $config
     * @return string
     */
    public static function getDumpDir(ContainerConfig $config = null)
    {
        if ($config) {
            return $config->getDumpDirectory();
        }

        return null;
    }
}
