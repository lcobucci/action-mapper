<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events\Listeners;

use Lcobucci\ActionMapper\Errors\ErrorHandler;
use Lcobucci\ActionMapper\Events\ExceptionEvent;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ExceptionProcessor
{
    /**
     * @var ErrorHandler
     */
    private $handler;

    /**
     * @param ErrorHandler $handler
     */
    public function __construct(ErrorHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param ExceptionEvent $event
     */
    public function process(ExceptionEvent $event)
    {
        $this->handler->handle(
            $event->getRequest(),
            $event->getResponse(),
            $event->getException()
        );
    }
}
