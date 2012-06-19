<?php
namespace Lcobucci\ActionMapper2\Filter;

use Lcobucci\ActionMapper2\Http\Response;

use Lcobucci\ActionMapper2\Http\Request;

interface Filter
{
	/**
	 * @param \Lcobucci\ActionMapper2\Http\Request $request
	 */
	public function applyFilter(Request $request, Response $response);
}