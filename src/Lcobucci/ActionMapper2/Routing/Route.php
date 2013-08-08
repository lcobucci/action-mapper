<?php
namespace Lcobucci\ActionMapper2\Routing;

use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Http\Request;
use Lcobucci\ActionMapper2\Application;

interface Route
{
    /**
     * @param Application $application
     */
    public function setApplication(Application $application);

    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @param Response $response
     */
    public function setResponse(Response $response);
}
