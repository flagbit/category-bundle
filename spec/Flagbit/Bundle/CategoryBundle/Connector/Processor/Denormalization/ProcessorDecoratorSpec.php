<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Connector\Processor\Denormalization;

use Akeneo\Pim\Enrichment\Component\Category\Model\CategoryInterface;
use Akeneo\Tool\Component\Batch\Item\InvalidItemException;
use Akeneo\Tool\Component\Connector\Processor\Normalization\Processor;
use Flagbit\Bundle\CategoryBundle\Connector\Processor\Denormalization\ProcessorDecorator;
use Flagbit\Bundle\CategoryBundle\Transformer\DenormalizationTransformerManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @method process($item)
 */
class ProcessorDecoratorSpec extends ObjectBehavior
{
    /**
     * @param ParameterBag<string, array<string, mixed>> $propertiesBag
     */
    public function let(
        Processor $baseProcessor,
        DenormalizationTransformerManager $transformerManager,
        ParameterBag $propertiesBag
    ): void {
        $this->beConstructedWith($baseProcessor, $transformerManager, $propertiesBag);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProcessorDecorator::class);
    }

    /**
     * @param ParameterBag<string, array<string, mixed>> $propertiesBag
     *
     * @throws InvalidItemException
     * @throws ExceptionInterface
     */
    public function it_not_runs_transformations_without_properties(
        CategoryInterface $category,
        Processor $baseProcessor,
        DenormalizationTransformerManager $transformerManager,
        ParameterBag $propertiesBag
    ): void {
        $baseProcessor->process($category)->willReturn(['code' => 'test']);

        $propertiesBag->all()->willReturn([]);
        $transformerManager->transformOnDenormalized(Argument::any())->shouldNotBeCalled();
        $propertiesBag->set(Argument::type('string'), Argument::any())->shouldNotBeCalled();

        $this->process($category)->shouldReturn(['code' => 'test']);
    }

    /**
     * @param ParameterBag<string, array<string, mixed>> $propertiesBag
     *
     * @throws InvalidItemException
     */
    public function it_runs_transformations_when_properties_are_available(
        CategoryInterface $category,
        Processor $baseProcessor,
        DenormalizationTransformerManager $transformerManager,
        ParameterBag $propertiesBag
    ): void {
        $propertiesData            = [
            'category1' => ['test' => ['null' => ['data' => 'test', 'locale' => 'null']]],
            'category2' => ['test_2' => ['null' => ['data' => '1', 'locale' => 'null']]],
        ];
        $transformedPropertiesData = [
            ['test' => ['null' => ['data' => 'test', 'locale' => 'null']]],
            ['test_2' => ['null' => ['data' => 1, 'locale' => 'null']]],
        ];

        $baseProcessor->process($category)->willReturn(['code' => 'test']);

        $propertiesBag->all()->willReturn($propertiesData);
        $transformerManager->transformOnDenormalized($propertiesData['category1'])
            ->willReturn($transformedPropertiesData[0]);
        $transformerManager->transformOnDenormalized($propertiesData['category2'])
            ->willReturn($transformedPropertiesData[1]);
        $propertiesBag->set('category1', $transformedPropertiesData[0])
            ->shouldBeCalled();
        $propertiesBag->set('category2', $transformedPropertiesData[1])
            ->shouldBeCalled();

        $this->process($category)->shouldReturn(['code' => 'test']);
    }
}
