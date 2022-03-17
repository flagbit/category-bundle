<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle;

use Flagbit\Bundle\CategoryBundle\DependencyInjection\CompilerPass\DataTransformerCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FlagbitCategoryBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new DataTransformerCompilerPass());
    }
}
