<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

use ErrorException;
use Exception;
use Lcobucci\ActionMapper\Http\Response;
use Lcobucci\ActionMapper\Http\Request;
use Psr\Log\LoggerInterface;

/**
 * Base class to handle errors
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class ErrorHandler
{
    /**
     * The HTTP request
     *
     * @var Request
     */
    protected $request;

    /**
     * The HTTP response
     *
     * @var Response
     */
    protected $response;

    /**
     * The logger
     *
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
     * Configures the HTTP request
     *
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Configures the HTTP response
     *
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Configures the logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Handles errors
     *
     * @param int $severity
     * @param string $message
     * @param string $fileName
     * @param int $lineNumber
     */
    public function handlePhpError($severity, $message, $fileName, $lineNumber)
    {
        if ($this->shouldSkipError($severity, $message)) {
            return ;
        }

        throw $this->createErrorException($severity, $message, $fileName, $lineNumber);
    }

    /**
     * Handles fatal errors
     */
    public function handleFatalErrors()
    {
        if ($error = error_get_last()) {
            $this->handle(
                $this->createErrorException(
                    $error['type'],
                    $error['message'],
                    $error['file'],
                    $error['line']
                )
            );

            $this->response->prepare($this->request);
            $this->response->send();
        }
    }

    /**
     * Changes the default PHP error handler (every error will be an exception)
     */
    protected function changePhpErrorHandler()
    {
        set_error_handler(array($this, 'handlePhpError'));
        register_shutdown_function(array($this, 'handleFatalErrors'));
    }

    /**
     * Creates a new error exception
     *
     * @param int $severity
     * @param string $message
     * @param string $fileName
     * @param int $lineNumber
     * @return ErrorException
     */
    protected function createErrorException(
        $severity,
        $message,
        $fileName,
        $lineNumber
    ) {
        return new ErrorException(
            $message,
            0,
            $severity,
            $fileName,
            $lineNumber
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
     * Log the given exception (when logger is configured)
     *
     * @param Exception $error
     */
    protected function logError(Exception $error)
    {
        if ($this->logger === null) {
            return ;
        }

        if ($error instanceof InternalServerError
            && $error->getPrevious() instanceof ErrorException) {
            return $this->logger->critical($error->getMessage(), array('exception' => $error->getPrevious()));
        }

        if ($error instanceof InternalServerError) {
            return $this->logger->error($error->getMessage(), array('exception' => $error->getPrevious()));
        }

        $this->logger->warning($error->getMessage(), array('exception' => $error));
    }

    /**
     * Renders the error page according with the exception
     *
     * @param HttpException $error
     * @return string
     */
    abstract protected function getErrorContent(HttpException $error);

    /**
     * Verifies if the handler should ignore the given error
     *
     * @param int $severity
     * @param string $message
     * @return boolean
     */
    protected function shouldSkipError($severity, $message)
    {
        return false;
    }
}
