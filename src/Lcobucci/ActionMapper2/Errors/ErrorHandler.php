<?php
namespace Lcobucci\ActionMapper2\Errors;

use \Lcobucci\ActionMapper2\Http\Response;
use \Lcobucci\ActionMapper2\Http\Request;
use \ErrorException;
use \Exception;

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
     *
     * @param \Lcobucci\ActionMapper2\Http\Request $request
     * @param \Lcobucci\ActionMapper2\Http\Response $response
     * @param \Exception $error
     */
    final public function handle(
        Request $request,
        Response $response,
        Exception $error
    ) {
        if (!$error instanceof HttpException) {
            $error = new InternalServerError('Internal error occurred', null, $error);
        }

        $response->setStatusCode($error->getStatusCode());
        $response->setContent($this->getErrorContent($request, $response, $error));
    }

    /**
     * Renders the error page according with the exception
     *
     * @param \Lcobucci\ActionMapper2\Http\Request $request
     * @param \Lcobucci\ActionMapper2\Http\Response $response
     * @param \Lcobucci\ActionMapper2\Errors\HttpException $error
     */
    abstract protected function getErrorContent(
        Request $request,
        Response $response,
        HttpException $error
    );
}
