<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection\Handlers;

use Lcobucci\ActionMapper\Errors\DefaultHandler;
use Lcobucci\DependencyInjection\Config\Handler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Errors implements Handler
{
    const HANDLER = 'app.errorHandler';

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerBuilder $builder)
    {
        if ($builder->hasDefinition(static::HANDLER) || $builder->hasAlias(static::HANDLER)) {
            return;
        }

        $builder->register(static::HANDLER, DefaultHandler::class)
                ->addArgument(null)
                ->addArgument('%app.devmode%');
    }
}
