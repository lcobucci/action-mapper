<?php
namespace Lcobucci\ActionMapper\HttpErrors;

class PageNotFoundException extends HttpException
{
	/**
	 * @return string
	 */
	public function getStatusCode()
	{
		return '404 Not Found';
	}
}