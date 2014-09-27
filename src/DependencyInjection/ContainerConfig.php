<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection;

use Lcobucci\DependencyInjection\ContainerConfig as DefaultConfig;

/**
 * This is the basic configuration to build the dependency injection container
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ContainerConfig extends DefaultConfig
{
    /**
     * The default dependency container
     *
     * @var string
     */
    const DEFAULT_CONTAINER = '\Lcobucci\ActionMapper\DependencyInjection\Container';

    /**
     * @return string
     */
    public function getBaseClass()
    {
        return parent::getBaseClass() ?: self::DEFAULT_CONTAINER;
    }
}
