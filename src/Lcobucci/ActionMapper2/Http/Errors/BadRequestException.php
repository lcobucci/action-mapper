<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

class BadRequestException extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '400 Bad Request';
	}
}