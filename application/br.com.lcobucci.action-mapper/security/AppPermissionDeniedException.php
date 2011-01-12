<?php
/**
 * Contém a exception que será lançada quando não houver permissão para realizar a requisição
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage security
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Exception que será lançada quando não houver permissão para realizar a requisição
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage security
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppPermissionDeniedException extends AppSecurityException
{
}