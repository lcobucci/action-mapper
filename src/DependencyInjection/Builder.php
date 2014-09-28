<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection;

use Lcobucci\DependencyInjection\ContainerBuilder;
use Lcobucci\ActionMapper\DependencyInjection\Handlers\Events;
use Lcobucci\ActionMapper\DependencyInjection\Handlers\Errors;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Builder extends ContainerBuilder
{
    /**
     * {@inheritdoc}
     */
    protected function setDefaultConfiguration()
    {
        parent::setDefaultConfiguration();
        parent::setBaseClass(BaseContainer::class);

        $this->addHandler(new Errors());
        $this->addHandler(new Events());
    }
}
