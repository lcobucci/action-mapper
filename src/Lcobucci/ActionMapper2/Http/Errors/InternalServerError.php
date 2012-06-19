<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

class InternalServerError extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '500 Internal Server Error';
	}
}