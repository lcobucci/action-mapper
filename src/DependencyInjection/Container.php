<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection;

use Lcobucci\ActionMapper\Http\RequestAware;
use Lcobucci\ActionMapper\Http\ResponseAware;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface Container extends ContainerInterface, RequestAware, ResponseAware
{
}
