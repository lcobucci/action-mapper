<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events\Listeners;

use Lcobucci\ActionMapper\Events\ApplicationEvent;

/**
 * @author Luís Otávio Cobucci Oblonczyk
 */
class ApplicationFinisher
{
    /**
     * @param ApplicationEvent $event
     */
    public function finish(ApplicationEvent $event)
    {
        exit();
    }
}
