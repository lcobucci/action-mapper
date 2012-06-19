<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

abstract class HttpException extends \Exception
{
	/**
	 * @return string
	 */
	public abstract function getStatusCode();
}