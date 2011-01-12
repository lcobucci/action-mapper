<?php
/**
 * Contém a classe que gerencia os filtros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage filter
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe que centraliza os filtros a serem aplicados no sistema antes do redirecionamento da requisição
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage filter
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppFilterChain
{
	/**
	 * @var array
	 */
	private $filters;
	
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		$this->filters = array();
	}
	
	/**
	 * Retorna os filtros existentes
	 * 
	 * @return array
	 */
	protected function getFilters()
	{
		return $this->filters;
	}
	
	/**
	 * Adiciona um novo filtro à lista
	 * 
	 * @param AppFilter $filter
	 */
	public function attachFilter($uri, AppFilter $filter)
	{
		$id = count($this->filters);
		
		$this->filters[$id . ';' . $uri] = $filter;
	}
	
	/**
	 * Aplica os filtros do sistema
	 * 
	 * @param AppRequest $request
	 */
	public function applyFilters(AppRequest $request)
	{
		foreach ($this->getFiltersByUri($request->getUri()) as $filter) {
			$filter->applyFilter($request);
		}
	}
	
	/**
	 * @param string $uri
	 * @return AppFilter[]
	 */
	public function getFiltersByUri($uri)
	{
		$filters = array();
		
		foreach ($this->getFilters() as $filterUri => $filter) {
			$filterUri = explode(';', $filterUri);
			$filterUri = $filterUri[1];
			
			if ($this->compareUri($uri, $filterUri)) {
				$filters[] = $filter;
			}
		}
		
		return $filters;
	}
	
	/**
	 * Realiza a comparação entre as URIs
	 * 
	 * @param string $requestUri
	 * @param string $filterUri
	 * @return boolean
	 */
	protected function compareUri($requestUri, $filterUri)
	{
		return preg_match($this->uriToRegex($filterUri), $requestUri) == 1;
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
}