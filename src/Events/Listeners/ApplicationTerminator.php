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
class ApplicationTerminator
{
    /**
     * @param ApplicationEvent $event
     */
    public function terminate(ApplicationEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();

        $response->prepare($request);
        $response->send();

        $this->finish();
    }

    protected function finish()
    {
        exit();
    }
}
