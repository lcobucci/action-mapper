<?php
/**
 * This file is part of Action Mapper 2, a PHP 5.3+ front-controller
 * microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper2\Routing\Annotation;

/**
 * This annotation allows to inject services on filters and controllers
 *
 * @Annotation
 * @Target({"METHOD"})
 *
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Inject
{
    /**
     * @var array<string>
     */
    private $services;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (isset($options['value'])) {
            $this->services = (array) $options['value'];
        }
    }

    /**
     * @return array<string>
     */
    public function getServices()
    {
        return $this->services;
    }
}
