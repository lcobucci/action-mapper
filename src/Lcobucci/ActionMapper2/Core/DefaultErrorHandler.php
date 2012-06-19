<?php
namespace Lcobucci\ActionMapper2\Core;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Errors\HttpException;
use Lcobucci\ActionMapper2\Http\Request;

class DefaultErrorHandler extends AbstractErrorHandler
{
	/**
     * @see Lcobucci\ActionMapper2\Core\AbstractErrorHandler::renderErrorPage()
     */
    protected function renderErrorPage(Request $request, Response $response, HttpException $e)
    {
    	$response->setContent('<pre>'. $e . '</pre>');
    }
}