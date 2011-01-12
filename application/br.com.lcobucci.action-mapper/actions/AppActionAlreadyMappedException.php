<?php
/**
 * Contém a exception que será lançada quando uma ação já foi mapeada para uma URI
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 * @internal $Rev: 5 $ $LastChangedDate: 2010-10-15 15:52:26 -0300 (Sex, 15 Out 2010) $ $LastChangedBy: luis $
 */

/**
 * Exception que será lançada quando a URI já possui uma ação mapeada
 * 
 * @package br.com.lcobucci.action-mapper
 * @subpackage actions
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class AppActionAlreadyMappedException extends AppActionMapperException
{
}