<?php
namespace Lcobucci\ActionMapper2\Http\Errors;

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