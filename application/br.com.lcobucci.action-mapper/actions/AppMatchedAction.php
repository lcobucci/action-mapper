<?php
/**
 * Contém a classe utilizada quando uma mapeada ação é encontrada
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 6 $ $LastChangedDate: 2010-10-15 16:03:30 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe de retorno das buscas de ações mapeadas
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppMatchedAction
{
	/**
	 * Ação encontrada
	 * 
	 * @var AppAction
	 */
	private $action;
	
	/**
	 * URI mapeada
	 * 
	 * @var string
	 */
	private $uri;
	
	/**
	 * Construtor da classe
	 * 
	 * @param AppAction $action
	 * @param string $uri
	 */
	public function __construct(AppAction $action = null, $uri = null)
	{
		$this->setAction($action);
		$this->setUri($uri);
	}
	
	/**
	 * Retorna ação
	 * 
	 * @return AppAction
	 */
	public function getAction()
	{
		return $this->action;
	}

	/**
	 * Configura a ação
	 * 
	 * @param AppAction $action
	 */
	public function setAction(AppAction $action = null)
	{
		$this->action = $action;
	}

	/**
	 * Retorna a URI
	 * 
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * Configura a URI
	 * 
	 * @param string $uri
	 */
	public function setUri($uri = null)
	{
		$this->uri = $uri;
	}
}