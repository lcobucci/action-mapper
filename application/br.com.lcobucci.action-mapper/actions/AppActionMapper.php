<?php
/**
 * Contém a classe que gerencia as ações do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 17 $ $LastChangedDate: 2010-11-18 15:43:50 -0200 (Qui, 18 Nov 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe que gerencia o roteamento das ações do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppActionMapper
{
	/**
	 * Ações do sistema
	 * 
	 * @var array
	 */
	private $actions;
	
	/**
	 * Prefixo das classes (para mapeamento automático)
	 * 
	 * @var string
	 */
	private $classPrefix;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		$this->actions = array();
		$this->setClassPrefix('');
	}
	
	/**
	 * Retorna a lista de ações mapeadas
	 * 
	 * @return ArrayObject
	 */
	public function getActions()
	{
		return $this->actions;
	}
	
	/**
	 * Retorna o prefixo das classes para o mapeamento automático
	 * 
	 * @return string
	 */
	public function getClassPrefix()
	{
		return $this->classPrefix;
	}

	/**
	 * Configra o prefixo das classes para o mapeamento automático
	 * 
	 * @param string $classPrefix
	 */
	public function setClassPrefix($classPrefix)
	{
		$this->classPrefix = $classPrefix;
	}

	/**
	 * Adiciona manualmente uma ação ao sistema
	 * 
	 * @param string $uri URI da ação
	 * @param AppAction $action Controller da ação
	 * @throws AppActionAlreadyMappedException Quando já existe uma ação mapeada para a URI
	 */
	public function attachAction($uri, AppAction $action)
	{
		if (isset($this->actions[$uri])) {
			throw new AppActionAlreadyMappedException('Já existe uma ação mapeada para a URI "' . $uri . '"');
		}
		
		$this->actions[$uri] = $action;
	}
	
	/**
	 * Remove um mapeamento da lista
	 * 
	 * @param string $uri
	 * @throws AppActionNotFoundException Quando não é encontrada nenhuma ação para a URI
	 */
	public function dettachAction($uri)
	{
		if (!isset($this->actions[$uri])) {
			throw new AppActionNotFoundException('Nenhuma ação mapeada para a URI "' . $uri . '"');
		}
		
		unset($this->actions[$uri]);
	}
	
	/**
	 * Busca uma ação através da URI
	 * 
	 * @param string $uri
	 * @return AppMatchedAction
	 */
	public function getActionByUri($uri)
	{
		$action = null;
		$longestUri = null;
		
		foreach ($this->getActions() as $actionUri => $actionInstance) {
			if ($this->compareUri($uri, $actionUri) && strlen($actionUri) > strlen($longestUri)) {
				$longestUri = $actionUri;
				$action = $actionInstance;
			}
		}
		
		return is_null($action) ? null : new AppMatchedAction($action, $longestUri);
	}
	
	/**
	 * Realiza a comparação entre as URIs
	 * 
	 * @param string $requestUri
	 * @param string $actionUri
	 * @return boolean
	 */
	protected function compareUri($requestUri, $actionUri)
	{
		return preg_match($this->uriToRegex($actionUri), $requestUri) == 1;
	}
	
	/**
	 * Converte a URI em expressão regular
	 * 
	 * @param string $uri
	 * @return string
	 */
	protected function uriToRegex($uri)
	{
		$regex = str_replace('.', '\\.', $uri);
		$regex = str_replace(array('*', '/'), array('.*', '\\/'), $regex);
		
		return '/^' . $regex . '$/';
	}
	
	/**
	 * Procura a ação mapeada para a URI da requisição
	 * 
	 * @param AppRequest $request
	 * @return AppAction
	 */
	protected function searchAction(AppRequest $request)
	{
		$action = null;
		$matchedAction = $this->getActionByUri($request->getUri());
		
		if (is_null($matchedAction) || $matchedAction->getUri() == '*') {
			$action = $this->findDefaultAction($request);
		}
		
		if (is_null($action) && !is_null($matchedAction)) {
			$action = $matchedAction->getAction();
		}
		
		return $action;
	}
	
	/**
	 * Busca a ação de acordo com a convenção criada
	 * 
	 * @param AppRequest $request
	 * @return AppAction
	 */
	protected function findDefaultAction(AppRequest $request)
	{
		$action = null;
		$className = explode('/', $request->getUri());
		
		$actionUri = $className[0];
		$className = $this->getClassPrefix() . ucfirst($className[0]) . 'ActionController';
		
		if (class_exists($className)) {
			$reflection = new ReflectionClass($className);
			
			if ($reflection->implementsInterface('AppAction')) {
				$action = new $className();
				$this->attachAction($actionUri, $action);
				$this->attachAction($actionUri . '/*', $action);
			}
		}
		
		return $action;
	}
	
	/**
	 * Realiza o processamento da ação de acordo com a requisição
	 * 
	 * @param AppRequest $request
	 * @throws AppActionNotFoundException
	 */
	public function process(AppRequest $request)
	{
		if ($action = $this->searchAction($request)) {
			$action->process($request);
		} else {
			throw new AppActionNotFoundException('Nenhuma ação encontrada para esta requisição');
		}
	}
}