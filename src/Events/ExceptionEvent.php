<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Events;

use Exception as BaseException;
use Lcobucci\ActionMapper\Http\Exception;
use Lcobucci\ActionMapper\Http\Server\InternalServerError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class ExceptionEvent extends Event
{
    /**
     * @var string
     */
    const EXCEPTION = 'app.exception';

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * @param Request $request
     * @param Response $response
     * @param BaseException $exception
     */
    public function __construct(Request $request, Response $response, BaseException $exception)
    {
        parent::__construct($request, $response);

        $this->exception = $this->convert($exception);
    }

    /**
     * @param BaseException $exception
     *
     * @return Exception
     */
    protected function convert(BaseException $exception)
    {
        if ($exception instanceof Exception) {
            return $exception;
        }

        return new InternalServerError('An internal error has occurred', null, $exception);
    }

    /**
     * @return Exception
     */
    public function getException()
    {
        return $this->exception;
    }
}
