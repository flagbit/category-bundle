<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer;

use Flagbit\Bundle\CategoryBundle\Entity\CategoryConfig;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryConfigRepository;
use Flagbit\Bundle\CategoryBundle\Transformer\Denormalization\BooleanTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\Denormalization\DefaultTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\DenormalizationTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\DenormalizationTransformerManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @method addTransformer(DenormalizationTransformer $transformer, string $type)
 * @method transformOnDenormalized(array $properties)
 */
class DenormalizationTransformerManagerSpec extends ObjectBehavior
{
    public function let(CategoryConfigRepository $categoryConfigRepository): void
    {
        $this->beConstructedWith($categoryConfigRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DenormalizationTransformerManager::class);
    }

    public function it_skips_transformers_if_no_category_properties_configured(
        CategoryConfigRepository $categoryConfigRepository,
        CategoryConfig $categoryConfig,
        DefaultTransformer $defaultTransformer,
        BooleanTransformer $booleanTransformer
    ): void {
        $categoryConfigRepository->findConfig()
            ->willReturn($categoryConfig);

        $categoryConfig->getConfig()->willReturn([]);

        $defaultTransformer->transform(Argument::any())->shouldNotBeCalled();

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            DenormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnDenormalized(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]])
            ->shouldReturn(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]]);
    }

    public function it_runs_transformations_with_default_transformer(
        CategoryConfigRepository $categoryConfigRepository,
        CategoryConfig $categoryConfig,
        DefaultTransformer $defaultTransformer,
        BooleanTransformer $booleanTransformer
    ): void {
        $categoryConfigRepository->findConfig()
            ->willReturn($categoryConfig);

        $categoryConfig->getConfig()->willReturn([
            'test' => ['type' => 'text'],
        ]);

        $booleanTransformer->transform(Argument::any())->shouldNotBeCalled();
        $defaultTransformer->transform('test')
            ->shouldBeCalled()
            ->willReturnArgument();

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            DenormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnDenormalized(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]])
            ->shouldReturn(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]]);
    }

    public function it_runs_transformations_with_custom_transformer(
        CategoryConfigRepository $categoryConfigRepository,
        CategoryConfig $categoryConfig,
        DefaultTransformer $defaultTransformer,
        BooleanTransformer $booleanTransformer
    ): void {
        $categoryConfigRepository->findConfig()
            ->willReturn($categoryConfig);

        $categoryConfig->getConfig()->willReturn([
            'test' => ['type' => 'checkbox'],
        ]);

        $defaultTransformer->transform(Argument::any())->shouldNotBeCalled();
        $booleanTransformer->transform('0')
            ->shouldBeCalled()
            ->willReturn(false);

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            DenormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnDenormalized(['test' => ['null' => ['data' => '0', 'locale' => 'null']]])
            ->shouldReturn(['test' => ['null' => ['data' => false, 'locale' => 'null']]]);
    }

    public function it_runs_transformations_with_mixed_transformers(
        CategoryConfigRepository $categoryConfigRepository,
        CategoryConfig $categoryConfig,
        DefaultTransformer $defaultTransformer,
        BooleanTransformer $booleanTransformer
    ): void {
        $categoryConfigRepository->findConfig()
            ->willReturn($categoryConfig);

        $categoryConfig->getConfig()->willReturn([
            'test' => ['type' => 'text'],
            'test2' => ['type' => 'checkbox'],
        ]);

        $defaultTransformer->transform('test')
            ->shouldBeCalled()
            ->willReturnArgument();
        $booleanTransformer->transform('0')
            ->shouldBeCalled()
            ->willReturn(false);

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            DenormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnDenormalized([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
            'test2' => ['null' => ['data' => '0', 'locale' => 'null']],
        ])->shouldReturn([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
            'test2' => ['null' => ['data' => false, 'locale' => 'null']],
        ]);
    }

    public function it_skips_configured_properties_which_are_not_in_data(
        CategoryConfigRepository $categoryConfigRepository,
        CategoryConfig $categoryConfig,
        DefaultTransformer $defaultTransformer,
        BooleanTransformer $booleanTransformer
    ): void {
        $categoryConfigRepository->findConfig()
            ->willReturn($categoryConfig);

        $categoryConfig->getConfig()->willReturn([
            'test' => ['type' => 'text'],
            'test2' => ['type' => 'checkbox'],
        ]);

        $defaultTransformer->transform('test')
            ->shouldBeCalled()
            ->willReturnArgument();
        $booleanTransformer->transform(Argument::any())
            ->shouldNotBeCalled();

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            DenormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnDenormalized([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
        ])->shouldReturn([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
        ]);
    }
}
