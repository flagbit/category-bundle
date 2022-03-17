<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\DependencyInjection\CompilerPass;

use Flagbit\Bundle\CategoryBundle\DependencyInjection\CompilerPass\DataTransformerCompilerPass;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @method process(ContainerBuilder $container)
 */
class DataTransformerCompilerPassSpec extends ObjectBehavior
{
    public function it_is_initialiable(): void
    {
        $this->shouldHaveType(DataTransformerCompilerPass::class);
    }

    public function it_has_no_transformer_managers(ContainerBuilder $container): void
    {
        $container->has('flagbit.category.data_transformer.normalization_transformer_manager')->willReturn(false);
        $container->has('flagbit.category.data_transformer.denormalization_transformer_manager')->willReturn(false);

        $container->findDefinition(Argument::any())->shouldNotBeCalled();
        $container->findTaggedServiceIds(Argument::any())->shouldNotBeCalled();
        $container->findDefinition(Argument::any())->shouldNotBeCalled();

        $this->process($container);
    }

    public function it_prepares_normalization_transformer_manager(
        ContainerBuilder $container,
        Definition $normalizationTransformerManagerDefinition
    ): void {
        $container->has('flagbit.category.data_transformer.normalization_transformer_manager')->willReturn(true);
        $container->has('flagbit.category.data_transformer.denormalization_transformer_manager')->willReturn(false);

        $container->findDefinition('flagbit.category.data_transformer.denormalization_transformer_manager')
            ->shouldNotBeCalled();
        $container->findDefinition('flagbit.category.data_transformer.normalization_transformer_manager')
            ->willReturn($normalizationTransformerManagerDefinition);

        $container->findTaggedServiceIds('flagbit.category.data_transformer.denormalization')->shouldNotBeCalled();
        $container->findTaggedServiceIds('flagbit.category.data_transformer.normalization')
            ->willReturn([
                'flagbit.category.data_transformer.normalization.default' => [
                    'flagbit.category.data_transformer.normalization' => ['type' => 'default'],
                ],
                'flagbit.category.data_transformer.normalization.checkbox' => [
                    'flagbit.category.data_transformer.normalization' => ['type' => 'checkbox'],
                ],
            ]);
        $normalizationTransformerManagerDefinition->addMethodCall(
            'addTransformer',
            Argument::that(static function (array $args): bool {
                return $args[0] instanceof Reference && ($args[1] === 'default' || $args[1] === 'checkbox');
            })
        )->shouldBeCalledTimes(2);

        $this->process($container);
    }

    public function it_prepares_denormalization_transformer_manager(
        ContainerBuilder $container,
        Definition $denormalizationTransformerManagerDefinition
    ): void {
        $container->has('flagbit.category.data_transformer.normalization_transformer_manager')->willReturn(false);
        $container->has('flagbit.category.data_transformer.denormalization_transformer_manager')->willReturn(true);

        $container->findDefinition('flagbit.category.data_transformer.normalization_transformer_manager')
            ->shouldNotBeCalled();
        $container->findDefinition('flagbit.category.data_transformer.denormalization_transformer_manager')
            ->willReturn($denormalizationTransformerManagerDefinition);

        $container->findTaggedServiceIds('flagbit.category.data_transformer.normalization')->shouldNotBeCalled();
        $container->findTaggedServiceIds('flagbit.category.data_transformer.denormalization')
            ->willReturn([
                'flagbit.category.data_transformer.denormalization.default' => [
                    'flagbit.category.data_transformer.denormalization' => ['type' => 'default'],
                ],
                'flagbit.category.data_transformer.denormalization.checkbox' => [
                    'flagbit.category.data_transformer.denormalization' => ['type' => 'checkbox'],
                ],
            ]);

        $denormalizationTransformerManagerDefinition->addMethodCall(
            'addTransformer',
            Argument::that(static function (array $args): bool {
                return $args[0] instanceof Reference && ($args[1] === 'default' || $args[1] === 'checkbox');
            })
        )->shouldBeCalledTimes(2);

        $this->process($container);
    }

    public function it_prepares_both_transformer_manager(
        ContainerBuilder $container,
        Definition $normalizationTransformerManagerDefinition,
        Definition $denormalizationTransformerManagerDefinition
    ): void {
        $container->has('flagbit.category.data_transformer.normalization_transformer_manager')->willReturn(true);
        $container->has('flagbit.category.data_transformer.denormalization_transformer_manager')->willReturn(true);

        $container->findDefinition('flagbit.category.data_transformer.denormalization_transformer_manager')
            ->willReturn($denormalizationTransformerManagerDefinition);
        $container->findDefinition('flagbit.category.data_transformer.normalization_transformer_manager')
            ->willReturn($normalizationTransformerManagerDefinition);

        $container->findTaggedServiceIds('flagbit.category.data_transformer.denormalization')
            ->willReturn([
                'flagbit.category.data_transformer.denormalization.default' => [
                    'flagbit.category.data_transformer.denormalization' => ['type' => 'default'],
                ],
                'flagbit.category.data_transformer.denormalization.checkbox' => [
                    'flagbit.category.data_transformer.denormalization' => ['type' => 'checkbox'],
                ],
            ]);
        $container->findTaggedServiceIds('flagbit.category.data_transformer.normalization')
            ->willReturn([
                'flagbit.category.data_transformer.normalization.default' => [
                    'flagbit.category.data_transformer.normalization' => ['type' => 'default'],
                ],
                'flagbit.category.data_transformer.normalization.checkbox' => [
                    'flagbit.category.data_transformer.normalization' => ['type' => 'checkbox'],
                ],
            ]);

        $denormalizationTransformerManagerDefinition->addMethodCall(
            'addTransformer',
            Argument::that(static function (array $args): bool {
                return $args[0] instanceof Reference && ($args[1] === 'default' || $args[1] === 'checkbox');
            })
        )->shouldBeCalledTimes(2);
        $normalizationTransformerManagerDefinition->addMethodCall(
            'addTransformer',
            Argument::that(static function (array $args): bool {
                return $args[0] instanceof Reference && ($args[1] === 'default' || $args[1] === 'checkbox');
            })
        )->shouldBeCalledTimes(2);

        $this->process($container);
    }
}
