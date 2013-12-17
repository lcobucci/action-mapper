<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

use ErrorException;
use Exception;
use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * Base class to handle errors
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class ErrorHandler
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changePhpErrorHandler();
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Changes the default PHP error handler (every error will be an exception)
     */
    protected function changePhpErrorHandler()
    {
        $instance = $this;

        set_error_handler(
            function ($severity, $message, $fileName, $lineNumber) use ($instance) {
                if ($instance->shouldSkipError($severity, $message)) {
                    return ;
                }

                throw new ErrorException(
                    $message,
                    0,
                    $severity,
                    $fileName,
                    $lineNumber
                );
            }
        );
    }

    /**
     * Handle the exception (converting to internal server error if needed) and
     * showing a the error content
     *
     * @param Exception $error
     */
    public function handle(Exception $error)
    {
        if (!$error instanceof HttpException) {
            $error = new InternalServerError($error->getMessage(), null, $error);
        }

        $this->response->setStatusCode($error->getStatusCode());
        $this->response->setContent($this->getErrorContent($error));

        $this->logError($error);
    }

    /**
     * @param Exception $error
     */
    protected function logError(Exception $error)
    {
        if ($this->logger === null) {
            return ;
        }

        if ($error instanceof InternalServerError
            && $error->getPrevious() instanceof ErrorException) {
            return $this->logger->critical($error->getMessage(), ['exception' => $error->getPrevious()]);
        }

        if ($error instanceof InternalServerError) {
            return $this->logger->error($error->getMessage(), ['exception' => $error->getPrevious()]);
        }

        $this->logger->warning($error->getMessage(), ['exception' => $error]);
    }

    /**
     * Renders the error page according with the exception
     *
     * @param HttpException $error
     * @return string
     */
    abstract protected function getErrorContent(HttpException $error);

    /**
     * @param int $severity
     * @param string $message
     * @return boolean
     */
    protected function shouldSkipError($severity, $message)
    {
        return false;
    }
}
