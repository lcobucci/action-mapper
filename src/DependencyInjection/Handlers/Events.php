<?php
/**
 * This file is part of Action Mapper, a PHP front-controller microframework
 *
 * @license http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 */

namespace Lcobucci\ActionMapper\DependencyInjection\Handlers;

use Lcobucci\ActionMapper\Events\ApplicationEvent;
use Lcobucci\ActionMapper\Events\Listeners\ApplicationTerminator;
use Lcobucci\ActionMapper\Events\Listeners\ErrorHandlerConfigurator;
use Lcobucci\ActionMapper\Events\Listeners\ExceptionProcessor;
use Lcobucci\ActionMapper\Events\ExceptionEvent;
use Lcobucci\DependencyInjection\Config\Handler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\DependencyInjection\Reference;
use Lcobucci\ActionMapper\Events\Listeners\ApplicationFinisher;

/**
 * @author Luís Otávio Cobucci Oblonczyk <lcobucci@gmail.com>
 */
class Events implements Handler
{
    const DISPATCHER = 'app.eventDispatcher';
    const ERROR_CONFIGURATOR = 'app.events.errorConfigurator';
    const EXCEPTION_PROCESSOR = 'app.events.exceptionProcessor';
    const APPLICATION_TERMINATOR = 'app.events.applicationTerminator';
    const APPLICATION_FINISHER = 'app.events.applicationFinisher';

    /**
     * {@inheritdoc}
     */
    public function __invoke(ContainerBuilder $builder)
    {
        $dispatcher = $this->registerDispatcher($builder);

        $this->registerListeners($builder);
        $this->appendListeners($dispatcher);
    }

    /**
     * @param string $id
     * @param ContainerBuilder $builder
     *
     * @return bool
     */
    private function exists($id, ContainerBuilder $builder)
    {
        return $builder->hasDefinition($id) || $builder->hasAlias($id);
    }

    /**
     * @param ContainerBuilder $builder
     *
     * @return Definition
     */
    protected function registerDispatcher(ContainerBuilder $builder)
    {
        if ($this->exists(static::DISPATCHER, $builder)) {
            return $builder->findDefinition(static::DISPATCHER);
        }

        return $builder->register(static::DISPATCHER, EventDispatcher::class);
    }

    /**
     * @param ContainerBuilder $builder
     */
    protected function registerListeners(ContainerBuilder $builder)
    {
        $this->registerErrorConfigurator($builder);
        $this->registerExceptionProcessor($builder);
        $this->registerApplicationTerminator($builder);
        $this->registerApplicationFinisher($builder);
    }

    /**
     * @param Definition $dispatcher
     */
    protected function appendListeners(Definition $dispatcher)
    {
        $this->addErrorConfigurator($dispatcher);
        $this->addExceptionProcessor($dispatcher);
        $this->addApplicationTerminator($dispatcher);
        $this->addApplicationFinisher($dispatcher);
    }

    /**
     * @param unknown $builder
     */
    private function registerErrorConfigurator(ContainerBuilder $builder)
    {
        if ($this->exists(static::ERROR_CONFIGURATOR, $builder)) {
            return;
        }

        $builder->register(static::ERROR_CONFIGURATOR, ErrorHandlerConfigurator::class);
    }

    /**
     * @param Definition $dispatcher
     */
    private function addErrorConfigurator(Definition $dispatcher)
    {
        $dispatcher->addMethodCall(
            'addListener',
            [ApplicationEvent::START, [new Reference(static::ERROR_CONFIGURATOR), 'configure'], 99]
        );
    }

    /**
     * @param unknown $builder
     */
    private function registerExceptionProcessor(ContainerBuilder $builder)
    {
        if ($this->exists(static::EXCEPTION_PROCESSOR, $builder)) {
            return;
        }

        $builder->register(static::EXCEPTION_PROCESSOR, ExceptionProcessor::class)
                ->addArgument(new Reference(Errors::HANDLER));
    }

    /**
     * @param Definition $dispatcher
     */
    private function addExceptionProcessor(Definition $dispatcher)
    {
        $dispatcher->addMethodCall(
            'addListener',
            [ExceptionEvent::EXCEPTION, [new Reference(static::EXCEPTION_PROCESSOR), 'process']]
        );
    }

    /**
     * @param unknown $builder
     */
    private function registerApplicationTerminator(ContainerBuilder $builder)
    {
        if ($this->exists(static::APPLICATION_TERMINATOR, $builder)) {
            return;
        }

        $builder->register(static::APPLICATION_TERMINATOR, ApplicationTerminator::class);
    }

    /**
     * @param Definition $dispatcher
     */
    private function addApplicationTerminator(Definition $dispatcher)
    {
        $dispatcher->addMethodCall(
            'addListener',
            [ApplicationEvent::TERMINATE, [new Reference(static::APPLICATION_TERMINATOR), 'terminate']]
        );
    }

    /**
     * @param unknown $builder
     */
    private function registerApplicationFinisher(ContainerBuilder $builder)
    {
        if ($this->exists(static::APPLICATION_FINISHER, $builder)) {
            return;
        }

        $builder->register(static::APPLICATION_FINISHER, ApplicationFinisher::class);
    }

    /**
     * @param Definition $dispatcher
     */
    private function addApplicationFinisher(Definition $dispatcher)
    {
        $dispatcher->addMethodCall(
            'addListener',
            [ApplicationEvent::TERMINATE, [new Reference(static::APPLICATION_FINISHER), 'finish'], -99]
        );
    }
}
