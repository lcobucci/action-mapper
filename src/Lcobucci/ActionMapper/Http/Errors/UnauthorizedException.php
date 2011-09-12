<?php
namespace Lcobucci\ActionMapper\HttpErrors;

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