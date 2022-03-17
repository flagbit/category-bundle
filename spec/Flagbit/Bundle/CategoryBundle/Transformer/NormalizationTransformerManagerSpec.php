<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer;

use Flagbit\Bundle\CategoryBundle\Entity\CategoryConfig;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryConfigRepository;
use Flagbit\Bundle\CategoryBundle\Transformer\Normalization\BooleanTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\Normalization\DefaultTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\NormalizationTransformer;
use Flagbit\Bundle\CategoryBundle\Transformer\NormalizationTransformerManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @method addTransformer(NormalizationTransformer $transformer, string $type)
 * @method transformOnNormalized(array $properties)
 */
class NormalizationTransformerManagerSpec extends ObjectBehavior
{
    public function let(CategoryConfigRepository $categoryConfigRepository): void
    {
        $this->beConstructedWith($categoryConfigRepository);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(NormalizationTransformerManager::class);
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
            NormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnNormalized(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]])
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
            NormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnNormalized(['test' => ['null' => ['data' => 'test', 'locale' => 'null']]])
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
        $booleanTransformer->transform(false)
            ->shouldBeCalled()
            ->willReturn('0');

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            NormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnNormalized(['test' => ['null' => ['data' => false, 'locale' => 'null']]])
            ->shouldReturn(['test' => ['null' => ['data' => '0', 'locale' => 'null']]]);
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
        $booleanTransformer->transform(false)
            ->shouldBeCalled()
            ->willReturn('0');

        // Add transformers
        $this->addTransformer(
            $defaultTransformer,
            NormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnNormalized([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
            'test2' => ['null' => ['data' => false, 'locale' => 'null']],
        ])->shouldReturn([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
            'test2' => ['null' => ['data' => '0', 'locale' => 'null']],
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
            NormalizationTransformerManager::DEFAULT_NORMALIZATION_TRANSFORMER
        );
        $this->addTransformer(
            $booleanTransformer,
            'checkbox'
        );

        // Actual test call
        $this->transformOnNormalized([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
        ])->shouldReturn([
            'test' => ['null' => ['data' => 'test', 'locale' => 'null']],
        ]);
    }
}
