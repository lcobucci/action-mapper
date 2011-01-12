<?php
/**
 * Contém a classe padrão para o gerenciamento de erros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Classe padrão para o gerenciamento de erros do sistema
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage app
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class GenericAppErrorHandler extends AppErrorHandler
{
	/**
	 * Renderiza a mensage lançada quando uma ação não é encontrada
	 * 
	 * @param AppRequest $request
	 * @param AppActionNotFoundException $e
	 */
	protected function pageNotFoundResponse(AppRequest $request, AppActionNotFoundException $e)
	{
		echo $this->renderErrorMsg(
			'404 - Página não encontrada',
			'Não foi possível encontrar a página',
			$request,
			$e
		);
	}

	/**
	 * Renderiza a mensagem lançada quando o usuário não possui permissão
	 * 
	 * @param AppRequest $request
	 * @param AppPermissionDeniedException $e
	 */
	protected function permissionDeniedResponse(AppRequest $request, AppPermissionDeniedException $e)
	{
		echo $this->renderErrorMsg(
			'403 - Permissão negada',
			'Você não possui permissão para visualizar a página',
			$request,
			$e
		);
	}

	/**
	 * Renderiza a mensagem lançada quando ocorreu algum erro durante o processamento
	 * 
	 * @param AppRequest $request
	 * @param Exception $e
	 */
	protected function internalServerErrorResponse(AppRequest $request, Exception $e)
	{
		echo $this->renderErrorMsg(
			'500 - Erro interno',
			'Ocorreram erros durante o carregamento da página',
			$request,
			$e
		);
	}
	
	/**
	 * Retorna o HTML da tela de erro
	 * 
	 * @param string $title
	 * @param string $errorDetails
	 * @param AppRequest $request
	 * @param Exception $e
	 * @return string
	 */
	protected function renderErrorMsg($title, $errorDetails, AppRequest $request, Exception $e)
	{
		return
			'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
					<title>' . $title . '</title>
					<style>
					body {
						margin: 0;
						background-color: #F0EBE2;
						text-align: center;
						font-size: 12px;
						font-family: Tahoma, Verdana, sans-serif;
						color: #444444;
					}
					
					#errorPageContainer {
						border: 1px solid #AAAAAA;
						background-color: #FFFFFF;
						padding: 40px;
						width: 45%;
						margin: auto;
						margin-top: 53px;
						text-align: left; 
					}
					
					#errorTitle {
						font-size: 20px;
						font-weight: bold;
						padding-bottom: 4px;
					}
					
					#errorLongContent {
						font-size: 14px;
						margin-top: 25px;
						margin-bottom: 16px;
						padding-bottom: 16px;
					}
					
					#errorDetails {
						overflow-y: scroll;
						margin-top: 15px;
						margin-left: 15px;
						padding: 5px;
						border: 1px solid #CCCCCC;
						height: 100px;
					}
					
					.bordered {
						border-bottom: 1px solid #AAAAAA;
					}
					
					.margin20 {
						margin-left: 20px;
					}
					</style>
				</head>
				<body>
					<div id="errorPageContainer">
						<div id="errorTitle" class="bordered">' . $title . '</div>
						<div id="errorLongContent" class="bordered margin20">
							' . $errorDetails . ' <strong>' . $request->getApplication()->getApplicationUri() . $request->getUri() . '</strong>.
						</div>
						<div class="margin20">
							<strong>Veja abaixo os detalhes do erro:</strong>
							<div id="errorDetails">
								' . nl2br($e) . '
							</div>
						</div>
					</div>
				</body>
			</html>';
	}
}