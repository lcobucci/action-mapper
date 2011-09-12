<?php
namespace Lcobucci\ActionMapper\HttpErrors;

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