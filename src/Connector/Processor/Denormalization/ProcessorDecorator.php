<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Connector\Processor\Denormalization;

use Akeneo\Pim\Enrichment\Component\Category\Model\CategoryInterface;
use Akeneo\Tool\Component\Batch\Item\InvalidItemException;
use Akeneo\Tool\Component\Batch\Item\ItemProcessorInterface;
use Akeneo\Tool\Component\Connector\Processor\Normalization\Processor;
use Flagbit\Bundle\CategoryBundle\Transformer\DenormalizationTransformerManager;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Decorator to transform category properties during denormalization.
 *
 * This decorator class can be used to decorate Akeneo's processor service
 * used to denormalize data for the category import.
 * It runs the defined transformers for the available category properties.
 *
 * @see Processor
 */
class ProcessorDecorator implements ItemProcessorInterface
{
    protected ItemProcessorInterface $baseProcessor;
    protected DenormalizationTransformerManager $transformerManager;
    /** @var ParameterBag<string, array<string, mixed>> */
    private ParameterBag $propertiesBag;

    /**
     * @param ParameterBag<string, array<string, mixed>> $propertiesBag
     */
    public function __construct(
        ItemProcessorInterface $baseProcessor,
        DenormalizationTransformerManager $transformerManager,
        ParameterBag $propertiesBag
    ) {
        $this->baseProcessor      = $baseProcessor;
        $this->transformerManager = $transformerManager;
        $this->propertiesBag      = $propertiesBag;
    }

    /**
     * @phpstan-param CategoryInterface $item
     *
     * @return mixed
     *
     * @throws InvalidItemException
     */
    public function process($item)
    {
        $originalResult = $this->baseProcessor->process($item);

        foreach ($this->propertiesBag->all() as $code => $properties) {
            $this->propertiesBag->set($code, $this->transformerManager->transformOnDenormalized($properties));
        }

        return $originalResult;
    }
}
