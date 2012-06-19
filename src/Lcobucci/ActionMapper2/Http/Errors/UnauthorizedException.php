<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

class UnauthorizedException extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '401 Unauthorized';
	}
}