<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Errors;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;
use ErrorException;
use Exception;

/**
 * Base class to handle errors
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class ErrorHandler
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changePhpErrorHandler();
    }

    /**
     * Changes the default PHP error handler (every error will be an exception)
     */
    protected function changePhpErrorHandler()
    {
        set_error_handler(
            function ($severity, $message, $fileName, $lineNumber) {
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
     * @param Request $request
     * @param Response $response
     * @param Exception $error
     */
    final public function handle(
        Request $request,
        Response $response,
        Exception $error
    ) {
        if (!$error instanceof HttpException) {
            $error = new InternalServerError($error->getMessage(), null, $error);
        }

        $response->setStatusCode($error->getStatusCode());
        $response->setContent($this->getErrorContent($request, $response, $error));
    }

    /**
     * Renders the error page according with the exception
     *
     * @param Request $request
     * @param Response $response
     * @param HttpException $error
     * @return string
     */
    abstract protected function getErrorContent(
        Request $request,
        Response $response,
        HttpException $error
    );
}
