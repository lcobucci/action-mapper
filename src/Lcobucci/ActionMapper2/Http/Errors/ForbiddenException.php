<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

class ForbiddenException extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '403 Forbidden';
	}
}