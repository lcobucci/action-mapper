<?php
namespace Lcobucci\ActionMapper2\Action;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;

interface Action
{
	/**
	 * @param Lcobucci\ActionMapper2\Http\Request $request
	 * @param Lcobucci\ActionMapper2\Http\Response $request
	 */
	public function process(Request $request, Response $response);
}