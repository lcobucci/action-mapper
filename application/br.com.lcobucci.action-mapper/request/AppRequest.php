<?php
/**
 * Contém a classe que representa as requisições HTTP
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage request
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 30 $ $LastChangedDate: 2010-12-03 16:45:24 -0200 (Sex, 03 Dez 2010) $ $LastChangedBy: gabriel $
 */

/**
 * Classe que representa os dados vindos das requisições HTTP
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage request
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppRequest
{
	/**
	 * Parâmetros passados na requisição
	 * 
	 * @var array
	 */
	private $vars;
	
	/**
	 * @var string
	 */
	private $uri;
	
	/**
	 * Retorna os parâmetros enviados
	 * 
	 * @return array
	 */
	public function getVars()
	{
		if (is_null($this->vars)) {
			$vars = $_REQUEST;
			
			if ($this->getHttpMethod() == 'PUT' || $this->getHttpMethod() == 'DELETE') {
				$vars = array_merge($vars, $this->getStreamVars());
			}
			
			$this->vars = $vars;
		}
		
		return $this->vars;
	}
	
	/**
	 * Retorna os parâmetros enviados pelos métodos PUT e DELETE
	 * 
	 * @return array
	 */
	protected function getStreamVars()
	{
		$putVars = array();
		parse_str(file_get_contents('php://input'), $putVars);
		
		return $putVars;
	}
	
	/**
	 * Retorna o método da requisição
	 * 
	 * @return string
	 */
	public function getHttpMethod()
	{
		return $_SERVER['REQUEST_METHOD'];
	}
	
	/**
	 * Retorna a URI da requisição
	 * 
	 * @return string
	 */
	public function getUri()
	{
		if (is_null($this->uri)) {
			$uri = parse_url($_SERVER['REQUEST_URI']);
			$uri = str_replace($this->getApplication()->getApplicationUri(), '', $uri['path']);
			
			$this->setUri($uri);
		}
		
		return $this->uri;
	}
	
	/**
	 * @param string $uri
	 */
	public function setUri($uri)
	{
		$this->uri = $uri;
	}
	
	/**
	 * Retorna os parâmetros adicionais da URI (removendo o primeiro segmento)
	 * 
	 * @return array
	 */
	public function getUriParams()
	{
		$params = explode('/', $this->getUri());
		array_shift($params);
		
		if (count($params) == 0) {
			$params[] = '';
		}
		
		return $params;
	}
	
	/**
	 * Retorna o valor do segmento de URI informado
	 * 
	 * @param int $segment
	 * @return string
	 */
	public function getUriSegment($segment)
	{
		if (preg_match('/^[0-9]{1,}$/', $segment) == 0) {
			throw new InvalidArgumentException('O segmento deve ser um numero natural');			
		}
		
		$uriSegments = explode('/', $this->getUri());
		
		if (isset($uriSegments[$segment])) {
			return $uriSegments[$segment]; 
		}

		return '';
	}
	
	/**
	 * Retorna a sessão
	 * 
	 * @return AppSession
	 */
	public function getSession()
	{
		return AppSession::getInstance();
	}
	
	/**
	 * Retorna a aplicação principal
	 * 
	 * @return WebApplication
	 */
	public function getApplication()
	{
		return WebApplication::getInstance();
	}
}