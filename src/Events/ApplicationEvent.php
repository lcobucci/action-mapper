<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ApplicationEvent extends Event
{
    /**
     * @var string
     */
    const START = 'app.start';

    /**
     * @var string
     */
    const TERMINATE = 'app.terminate';
}
