<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Transformer;

/**
 * Interface for normalization category property data transformers.
 */
interface NormalizationTransformer
{
    /**
     * Apply transformations to the supplied data on import.
     *
     * @param mixed $data a single property value
     */
    public function transform($data): string;
}
