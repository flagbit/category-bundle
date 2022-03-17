<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Load and manage the bundle configuration.
 */
class FlagbitCategoryExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @param mixed[] $configs
     *
     * @phpstan-return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('category_property_transformers.yml');
    }
}
