<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle;

use Flagbit\Bundle\CategoryBundle\DependencyInjection\CompilerPass\DataTransformerCompilerPass;
use Flagbit\Bundle\CategoryBundle\FlagbitCategoryBundle;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @method build(ContainerBuilder $containerBuilder)
 */
class FlagbitCategoryBundleSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(FlagbitCategoryBundle::class);
    }

    public function it_should_be_build_with_data_transformer_compiler_pass(ContainerBuilder $container): void
    {
        $container->addCompilerPass(Argument::type(DataTransformerCompilerPass::class))->shouldBeCalledOnce();

        $this->build($container);
    }
}
