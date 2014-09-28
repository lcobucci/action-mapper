<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Errors;

use Lcobucci\ActionMapper\Http\Exception;
use Psr\Log\LoggerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The definition of error handlers
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface ErrorHandler extends LoggerAwareInterface
{
    /**
     * Handle the exception (converting to internal server error if needed) and
     * showing a the error content
     *
     * @param Request $request
     * @param Response $response
     * @param Exception $exception
     */
    public function handle(Request $request, Response $response, Exception $exception);
}
