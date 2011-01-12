<?php
/**
 * Contém a classe base para o gerenciamento de erros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe base para o gerenciamento de erros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
abstract class AppErrorHandler
{
	/**
	 * Construtor da classe
	 */
	public function __construct()
	{
		$this->changePhpErrorHandler();
	}
	
	/**
	 * Altera gerenciador de erros default do PHP
	 */
	protected function changePhpErrorHandler()
	{
		if (!function_exists('handleError')) {
			function handleError($severity, $message, $fileName, $lineNumber)
			{
				throw new ErrorException($message, 0, $severity, $fileName, $lineNumber);
			}
			
			set_error_handler('handleError');
		}
	}
	
	/**
	 * Recebe as exceptions do sistema e mostra as mensagens de acordo
	 * com o tipo do erro
	 * 
	 * @param Exception $e
	 */
	public final function handleError(AppRequest $request, Exception $e)
	{
		if ($e instanceof AppActionNotFoundException) {
			$this->changeHttpStatus(404);
			$this->pageNotFoundResponse($request, $e);
		} elseif ($e instanceof AppPermissionDeniedException) {
			$this->changeHttpStatus(403);
			$this->permissionDeniedResponse($request, $e);
		} else {
			$this->changeHttpStatus(500);
			$this->internalServerErrorResponse($request, $e);
		}
	}
	
	/**
	 * Altera o status HTTP
	 * 
	 * @param int $status
	 */
	protected function changeHttpStatus($status)
	{
		header('HTTP/1.0 ' . $status);
	}
	
	/**
	 * Renderiza a mensage lançada quando uma ação não é encontrada
	 * 
	 * @param AppRequest $request
	 * @param AppActionNotFoundException $e
	 */
	protected abstract function pageNotFoundResponse(AppRequest $request, AppActionNotFoundException $e);
	
	/**
	 * Renderiza a mensagem lançada quando o usuário não possui permissão
	 * 
	 * @param AppRequest $request
	 * @param AppPermissionDeniedException $e
	 */
	protected abstract function permissionDeniedResponse(AppRequest $request, AppPermissionDeniedException $e);
	
	/**
	 * Renderiza a mensagem lançada quando ocorreu algum erro durante o processamento
	 * 
	 * @param AppRequest $request
	 * @param Exception $e
	 */
	protected abstract function internalServerErrorResponse(AppRequest $request, Exception $e);
}