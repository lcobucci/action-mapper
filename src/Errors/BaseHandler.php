<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

use ErrorException;
use Lcobucci\ActionMapper\Http\Exception;
use Lcobucci\ActionMapper\Http\Server\InternalServerError;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Base class to handle errors
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class BaseHandler implements ErrorHandler
{
    use LoggerAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Response $response, Exception $exception)
    {
        $response->setStatusCode($exception->getStatusCode());
        $response->setContent($this->getErrorContent($request, $response, $exception));

        $this->logError($exception);
    }

    /**
     * Log the given exception (when logger is configured)
     *
     * @param Exception $exception
     */
    private function logError(Exception $exception)
    {
        if ($this->logger === null) {
            return ;
        }

        $this->logger->log(
            $this->getLogLevel($exception),
            $exception->getMessage(),
            ['exception' => $exception]
        );
    }

    /**
     * @param Exception $exception
     *
     * @return string
     */
    protected function getLogLevel(Exception $exception)
    {
        if (!$exception instanceof InternalServerError) {
            return LogLevel::WARNING;
        }

        if ($exception->getPrevious() instanceof ErrorException) {
            return LogLevel::CRITICAL;
        }

        return LogLevel::ERROR;
    }

    /**
     * Renders the error page according with the exception
     *
     * @param Request $request
     * @param Response $response
     * @param Exception $exception
     *
     * @return string
     */
    abstract protected function getErrorContent(Request $request, Response $response, Exception $error);
}
