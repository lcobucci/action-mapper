<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events\Listeners;

use Closure;
use ErrorException;
use Lcobucci\ActionMapper\Events\ApplicationEvent;
use Lcobucci\ActionMapper\Events\ExceptionEvent;

/**
 * @author Luís Otávio Cobucci Oblonczyk
 */
class ErrorHandlerConfigurator
{
    /**
     * @param ApplicationEvent $event
     */
    public function configure(ApplicationEvent $event)
    {
        set_error_handler($this->getPhpHandler($event));
        register_shutdown_function($this->getFatalErrorHandler($event));
    }

    /**
     * @return Closure
     */
    protected function getPhpHandler(ApplicationEvent $event)
    {
        return function ($severity, $message, $fileName, $lineNumber) {
            if (error_reporting() == 0) {
                return;
            }

            throw $this->createErrorException($severity, $message, $fileName, $lineNumber);
        };
    }

    /**
     * {@inheritdoc}
    */
    protected function getFatalErrorHandler(ApplicationEvent $event)
    {
        return function () use ($event) {
            $error = error_get_last();

            if ($error === null) {
                return;
            }

            $dispatcher = $event->getDispatcher();

            $dispatcher->dispatch(
                ExceptionEvent::EXCEPTION,
                new ExceptionEvent(
                    $event->getRequest(),
                    $event->getResponse(),
                    $this->createErrorException(
                        $error['type'],
                        $error['message'],
                        $error['file'],
                        $error['line']
                    )
                )
            );

            $dispatcher->dispatch(ApplicationEvent::TERMINATE, $event);
        };
    }

    /**
     * Creates a new error exception
     *
     * @param int $severity
     * @param string $message
     * @param string $file
     * @param int $line
     *
     * @return ErrorException
     */
    public function createErrorException($severity, $message, $file, $line)
    {
        return new ErrorException($message, 0, $severity, $file, $line);
    }
}
