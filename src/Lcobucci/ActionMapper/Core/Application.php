<?php
namespace Lcobucci\ActionMapper\Core;

use Lcobucci\ActionMapper\Http\Request;
use Lcobucci\ActionMapper\Http\Response;
use Lcobucci\ActionMapper\Filter\FilterChain;
use Lcobucci\ActionMapper\Action\Mapper;
use Lcobucci\Session\SessionStorage;
use \Exception;

class Application
{
	/**
	 * @var Lcobucci\Session\SessionStorage
	 */
	private $session;

	/**
	 * @var Lcobucci\ActionMapper\Action\Mapper
	 */
	private $actionMapper;

	/**
	 * @var Lcobucci\ActionMapper\Core\AbstractErrorHandler
	 */
	private $errorHandler;

	/**
	 * @var Lcobucci\ActionMapper\Filter\FilterChain
	 */
	private $filterChain;

	/**
	 * @var Lcobucci\ActionMapper\Http\Request
	 */
	private $request;

	/**
	 * @var Lcobucci\ActionMapper\Http\Response
	 */
	private $response;

	/**
	 * @param Lcobucci\ActionMapper\Action\Mapper $actionMapper
	 * @param Lcobucci\ActionMapper\Filter\FilterChain $filterChain
	 * @param Lcobucci\ActionMapper\Core\AbstractErrorHandler $errorHandler
	 * @param Lcobucci\Session\SessionStorage $session
	 */
	public function __construct(Mapper $actionMapper, FilterChain $filterChain,
		AbstractErrorHandler $errorHandler, SessionStorage $session)
	{
		$this->actionMapper = $actionMapper;
		$this->filterChain = $filterChain;
		$this->errorHandler = $errorHandler;
		$this->session = $session;
	}

	/**
     * Returns the $errorHandler
     *
     * @return Lcobucci\ActionMapper\Core\AbstractErrorHandler
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
     * @return Lcobucci\ActionMapper\Action\Mapper
     */
    public function getActionMapper()
    {
        return $this->actionMapper;
    }

	/**
     * Returns the $filterChain
     *
     * @return Lcobucci\ActionMapper\Filter\FilterChain
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

		return dirname($uri['path']) . '/';
	}

	/**
	 * @return string
	 */
	public function getUrl()
	{
		return 'http://' . $_SERVER['HTTP_HOST'] . $this->getPath();
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
	 * @return \Lcobucci\ActionMapper\Http\Request
	 */
	protected function getRequest()
	{
		if (is_null($this->request)) {
			$this->request = new Request($this);
		}

		return $this->request;
	}

	/**
	 * @return \Lcobucci\ActionMapper\Http\Response
	 */
	protected function getResponse()
	{
		if (is_null($this->response)) {
			$this->response = new Response($this);
		}

		return $this->response;
	}
}