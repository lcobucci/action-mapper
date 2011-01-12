<?php
/**
 * Contém a classe gerenciamento das operaçãoes pela URI
 *
 * @package br.com.lcobucci.action-mapper
 * @subpackage request
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 11 $ $LastChangedDate: 2010-10-27 10:44:44 -0200 (Qua, 27 Out 2010) $ $LastChangedBy: gabriel $
 */

/**
 * Classe gerenciamento das operaçãoes pela URI
 *
 * @package br.com.lcobucci.action-mapper
 * @subpackage request
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class AppUriOperations
{
	/**
	 * Valida se a URI é válida
	 *
	 * @param AppRequest $request
	 * @throws AppActionNotFoundException
	 */
	protected function validateUri(AppRequest $request)
	{
		$uri = implode('/', $request->getUriParams());
		$actions = array_merge($this->getDefaultActions(), $this->getCustomActions());

		foreach ($actions as $action) {
			if (preg_match($action, $uri)) {
				return true;
			}
		}

		throw new AppActionNotFoundException('A URI informada não possui ação mapeada');
	}

	/**
	 * Ações padrão para URI (list, edit, delete, insert, update)
	 *
	 * @return array
	 */
	protected function getDefaultActions()
	{
		return array(
			'/^[\/]?$/',
			'/^(edit\/)+[0-9]{1,}$/',
			'/^(del\/)+[0-9]{1,}$/',
			'/^(new[\/]?)$/',
			'/^[0-9]{1,}$/',
		);
	}

	/**
	 * Ações personalizadas para a URI
	 *
	 * @return array
	 */
	protected function getCustomActions()
	{
		return array();
	}
}