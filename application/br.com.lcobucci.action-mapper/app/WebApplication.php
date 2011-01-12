<?php
/**
 * Contém a classe principal do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 31 $ $LastChangedDate: 2010-12-07 10:02:36 -0200 (Ter, 07 Dez 2010) $ $LastChangedBy: gabriel $
 */

/**
 * Classe que centraliza todas funcionalidades de front-controller para o sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class WebApplication
{
	/**
	 * Instância da classe
	 * 
	 * @var WebApplication
	 */
	private static $instance;
	
	/**
	 * Mapeamento das ações do sistema
	 * 
	 * @var AppActionMapper
	 */
	private $actionMapper;
	
	/**
	 * Cadeia de filtros a serem aplicados antes das ações do sistema
	 * 
	 * @var AppFilterChain
	 */
	private $filterChain;
	
	/**
	 * @var AppErrorHandler
	 */
	private $errorHandler;
	
	/**
	 * @var AppRequest
	 */
	private $lastRequest;
	
	/**
	 * Singleton usage
	 * 
	 * @return WebApplication
	 */
	public static function getInstance()
	{
		if (is_null(self::$instance)) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Construtor da classe 
	 */
	private function __construct()
	{
		$this->actionMapper = new AppActionMapper();
		$this->filterChain = new AppFilterChain();
		$this->errorHandler = new GenericAppErrorHandler();
	}
	
	/**
	 * Retorna o mapeamento das ações do sistema
	 * 
	 * @return AppActionMapper
	 */
	public function getActionMapper()
	{
		return $this->actionMapper;
	}
	
	/**
	 * Retorna a cadeia de filtros do sistema
	 * 
	 * @return AppFilterChain
	 */
	public function getFilterChain()
	{
		return $this->filterChain;
	}
	
	/**
	 * @return AppErrorHandler
	 */
	protected function getErrorHandler()
	{
		return $this->errorHandler;
	}
	
	/**
	 * @param AppErrorHandler $errorHandler
	 */
	public function setErrorHandler(AppErrorHandler $errorHandler)
	{
		$this->errorHandler = $errorHandler;
	}
	
	/**
	 * @return string
	 */
	public function getApplicationUri()
	{
		$uri = parse_url($_SERVER['PHP_SELF']);
		
		return dirname($uri['path']) . '/';
	}
	
	/**
	 * @param string $sessionName
	 * @return AppSession
	 */
	public function startSession($sessionName = null)
	{
		AppSession::getInstance()->start($sessionName);
		
		return AppSession::getInstance();
	}
	
	public function run()
	{
		try {
			$request = $this->getLastRequest();
			
			$this->getFilterChain()->applyFilters($request);
			$this->getActionMapper()->process($request);
		} catch (Exception $e) {
			$this->getErrorHandler()->handleError($request, $e);
		}
	}
	
	/**
	 * @return AppRequest
	 */
	public function getLastRequest()
	{
		if (is_null($this->lastRequest)) {
			$this->lastRequest = new AppRequest();
		}
		
		return $this->lastRequest;
	}
	
	/**
	 * Redireciona para determinada uri
	 * 
	 * @param string $uri
	 */
	public function redirect($uri)
	{
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $this->getApplicationUri() . $uri);
		exit;
	}
}