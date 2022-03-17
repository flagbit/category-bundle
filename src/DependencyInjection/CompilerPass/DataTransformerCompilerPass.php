<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass to pass {@see NormalizationTransformer}s to the {@see NormalizationTransformerManager}.
 */
class DataTransformerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->has('flagbit.category.data_transformer.normalization_transformer_manager')) {
            $this->collectNormalizationTransformers($container);
        }

        if (! $container->has('flagbit.category.data_transformer.denormalization_transformer_manager')) {
            return;
        }

        $this->collectDenormalizationTransformers($container);
    }

    /**
     * Collect all normalization transformers and register them.
     */
    private function collectNormalizationTransformers(ContainerBuilder $container): void
    {
        $normalizationDefinition = $container->findDefinition(
            'flagbit.category.data_transformer.normalization_transformer_manager'
        );
        $taggedServices          = $container->findTaggedServiceIds(
            'flagbit.category.data_transformer.normalization'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $normalizationDefinition->addMethodCall('addTransformer', [
                    new Reference($id),
                    $attributes['type'],
                ]);
            }
        }
    }

    /**
     * Collect all denormalization transformers and register them.
     */
    private function collectDenormalizationTransformers(ContainerBuilder $container): void
    {
        $denormalizationDefinition = $container->findDefinition(
            'flagbit.category.data_transformer.denormalization_transformer_manager'
        );
        $taggedServices            = $container->findTaggedServiceIds(
            'flagbit.category.data_transformer.denormalization'
        );
        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $denormalizationDefinition->addMethodCall('addTransformer', [
                    new Reference($id),
                    $attributes['type'],
                ]);
            }
        }
    }
}
