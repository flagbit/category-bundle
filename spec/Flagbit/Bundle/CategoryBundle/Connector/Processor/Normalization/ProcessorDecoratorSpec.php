<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Connector\Processor\Normalization;

use Akeneo\Pim\Enrichment\Component\Category\Model\CategoryInterface;
use Akeneo\Tool\Component\Batch\Item\InvalidItemException;
use Akeneo\Tool\Component\Connector\Processor\Normalization\Processor;
use Flagbit\Bundle\CategoryBundle\Connector\ArrayConverter\StandardToFlat\CategoryProperty as StandardToFlatConverter;
use Flagbit\Bundle\CategoryBundle\Connector\Processor\Normalization\ProcessorDecorator;
use Flagbit\Bundle\CategoryBundle\Entity\CategoryProperty;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryPropertyRepository;
use Flagbit\Bundle\CategoryBundle\Transformer\NormalizationTransformerManager;
use PhpSpec\ObjectBehavior;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @method process($item)
 */
class ProcessorDecoratorSpec extends ObjectBehavior
{
    public function let(
        CategoryPropertyRepository $categoryPropertyRepository,
        StandardToFlatConverter $standardToFlat,
        Processor $baseProcessor,
        NormalizationTransformerManager $transformerManager
    ): void {
        $this->beConstructedWith($categoryPropertyRepository, $standardToFlat, $baseProcessor, $transformerManager);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(ProcessorDecorator::class);
    }

    /**
     * @throws InvalidItemException
     * @throws ExceptionInterface
     */
    public function it_should_not_merge_properties_if_missing(
        CategoryInterface $item,
        CategoryPropertyRepository $categoryPropertyRepository,
        Processor $baseProcessor
    ): void {
        $categoryPropertyRepository->findByCategory($item)->willReturn(null);

        $baseProcessor->process($item)->willReturn([
            'code' => 'test',
            'parent' => 'master',
            'label' => [
                'de_DE' => 'Test',
                'en_EN' => 'Test',
            ],
        ]);

        $this->process($item)->shouldReturn([
            'code' => 'test',
            'parent' => 'master',
            'label' => [
                'de_DE' => 'Test',
                'en_EN' => 'Test',
            ],
        ]);
    }

    /**
     * @throws InvalidItemException
     */
    public function it_should_merge_properties_if_available(
        CategoryInterface $item,
        CategoryProperty $categoryProperty,
        CategoryPropertyRepository $categoryPropertyRepository,
        StandardToFlatConverter $standardToFlat,
        Processor $baseProcessor,
        NormalizationTransformerManager $transformerManager
    ): void {
        $categoryPropertyRepository->findByCategory($item)->willReturn($categoryProperty);

        $categoryProperty->getProperties()->willReturn([]);

        $transformerManager->transformOnNormalized([])
            ->shouldNotHaveBeenCalled()
            ->willReturn([]);

        $standardToFlat->convert([])->willReturn(['some' => 'data']);

        $baseProcessor->process($item)->willReturn([
            'code' => 'test',
            'parent' => 'master',
            'label' => [
                'de_DE' => 'Test',
                'en_EN' => 'Test',
            ],
        ]);

        $this->process($item)->shouldReturn([
            'code' => 'test',
            'parent' => 'master',
            'label' => [
                'de_DE' => 'Test',
                'en_EN' => 'Test',
            ],
            'some' => 'data',
        ]);
    }
}
