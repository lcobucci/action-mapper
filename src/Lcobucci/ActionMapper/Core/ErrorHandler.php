<?php
namespace Lcobucci\ActionMapper\Core;

use \ErrorException;

abstract class ErrorHandler
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->changePhpErrorHandler();
	}

	/**
	 * Changes the default PHP error handler (every error will be an exception)
	 */
	protected function changePhpErrorHandler()
	{
		set_error_handler(
			function($severity, $message, $fileName, $lineNumber)
			{
				throw new ErrorException($message, 0, $severity, $fileName, $lineNumber);
			}
		);
	}
}