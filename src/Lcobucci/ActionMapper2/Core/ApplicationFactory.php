<?php
namespace Lcobucci\ActionMapper2\Core;

use Lcobucci\Session\SessionStorage;

use Lcobucci\ActionMapper2\Filter\FilterChain;

use Lcobucci\ActionMapper2\Action\Mapper;

class ApplicationFactory
{
	public static function initApplication(AbstractErrorHandler $errorHandler = null)
	{
		if (is_null($errorHandler)) {
			$errorHandler = new DefaultErrorHandler();
		}

		return new Application(
			new Mapper(),
			new FilterChain(),
			$errorHandler,
			new SessionStorage()
		);
	}
}