<?php
namespace Lcobucci\ActionMapper2\Core;

use Lcobucci\ActionMapper2\Http\Request;
use Lcobucci\ActionMapper2\Http\Response;
use Lcobucci\ActionMapper2\Filter\FilterChain;
use Lcobucci\ActionMapper2\Action\Mapper;
use Lcobucci\Session\SessionStorage;
use \Exception;

class Application
{
	/**
	 * @var Lcobucci\Session\SessionStorage
	 */
	private $session;

	/**
	 * @var Lcobucci\ActionMapper2\Action\Mapper
	 */
	private $actionMapper;

	/**
	 * @var Lcobucci\ActionMapper2\Core\AbstractErrorHandler
	 */
	private $errorHandler;

	/**
	 * @var Lcobucci\ActionMapper2\Filter\FilterChain
	 */
	private $filterChain;

	/**
	 * @var Lcobucci\ActionMapper2\Http\Request
	 */
	private $request;

	/**
	 * @var Lcobucci\ActionMapper2\Http\Response
	 */
	private $response;

	/**
	 * @var object
	 */
	private $dependencyContainer;

	/**
	 * @param Lcobucci\ActionMapper2\Action\Mapper $actionMapper
	 * @param Lcobucci\ActionMapper2\Filter\FilterChain $filterChain
	 * @param Lcobucci\ActionMapper2\Core\AbstractErrorHandler $errorHandler
	 * @param Lcobucci\Session\SessionStorage $session
	 */
	public function __construct(Mapper $actionMapper, FilterChain $filterChain, AbstractErrorHandler $errorHandler, SessionStorage $session)
	{
		$this->actionMapper = $actionMapper;
		$this->filterChain = $filterChain;
		$this->errorHandler = $errorHandler;
		$this->session = $session;
	}

	/**
     * Returns the $errorHandler
     *
     * @return Lcobucci\ActionMapper2\Core\AbstractErrorHandler
     */
    public function getErrorHandler()
    {
        return $this->errorHandler;
    }

	/**
     * Returns the $session
     *
     * @return Lcobucci\Session\SessionStorage
     */
    public function getSession()
    {
        return $this->session;
    }

	/**
     * Returns the $actionMapper
     *
     * @return Lcobucci\ActionMapper2\Action\Mapper
     */
    public function getActionMapper()
    {
        return $this->actionMapper;
    }

	/**
     * Returns the $filterChain
     *
     * @return Lcobucci\ActionMapper2\Filter\FilterChain
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

	/**
	 * @return string
	 */
	public function getPath()
	{
		$uri = parse_url($_SERVER['PHP_SELF']);
		$uri = dirname($uri['path']);

		if (substr($uri, -1) != '/') {
		    $uri .= '/';
		}

		return $uri;
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return $this->getProtocol() . '://' . $_SERVER['HTTP_HOST']
			. $this->getPath();
	}

	/**
	 * Return if is http or https protocol
	 *
	 * @return string
	 */
	public function getProtocol()
	{
		if ((isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')
			|| $_SERVER['SERVER_PORT'] == 443) {
			return $protocol = 'https';
		}

		return 'http';
	}

	public function run()
	{
		try {
			$request = $this->getRequest();
			$response = $this->getResponse();

			$this->getFilterChain()->applyFilters($request, $response);
			$this->getActionMapper()->process($request, $response);
		} catch (Exception $e) {
			$this->getErrorHandler()->handleError($request, $response, $e);
		}

		$response->send();
	}

	/**
	 * @return object
	 */
	public function getDependencyContainer()
	{
		return $this->dependencyContainer;
	}

	/**
	 * @param object $dependencyContainer
	 */
	public function setDependencyContainer($dependencyContainer)
	{
		$this->dependencyContainer = $dependencyContainer;
	}

	/**
	 * @return \Lcobucci\ActionMapper2\Http\Request
	 */
	protected function getRequest()
	{
		if (is_null($this->request)) {
			$this->request = new Request($this);
		}

		return $this->request;
	}

	/**
	 * @return \Lcobucci\ActionMapper2\Http\Response
	 */
	protected function getResponse()
	{
		if (is_null($this->response)) {
			$this->response = new Response($this);
		}

		return $this->response;
	}
}