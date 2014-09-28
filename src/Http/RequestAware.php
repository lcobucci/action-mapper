<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\Http;

use Symfony\Component\HttpFoundation\Request;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
interface RequestAware
{
    /**
     * @param Request $request
     */
    public function setRequest(Request $request);
}
