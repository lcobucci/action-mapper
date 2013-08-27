<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Config;

use stdClass;

/**
 * The route loader is a basic interface that defines the standard way to load
 * routes from a configuration file
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface RouteLoader
{
    /**
     * Load the file and returns the configuration data
     *
     * @param string $file
     * @return stdClass
     */
    public function load($file);
}
