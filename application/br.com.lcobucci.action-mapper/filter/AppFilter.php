<?php
/**
 * Contém a inteface para os filtros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage filter
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Interface que define os métodos necessários de um filtro
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage filter
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface AppFilter
{
	/**
	 * Aplica o filtro na requisição
	 * 
	 * @param AppRequest $request
	 */
	public function applyFilter(AppRequest $request);
}