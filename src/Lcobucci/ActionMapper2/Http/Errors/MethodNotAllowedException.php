<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

class MethodNotAllowedException extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '405 Method Not Allowed';
	}
}