<?php
/**
 * Contém a inteface para as ações do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Interface que define os métodos necessários de uma ação
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface AppAction
{
	/**
	 * Realiza o processamento da requisição
	 * 
	 * @param AppRequest $request
	 */
	public function process(AppRequest $request);
}