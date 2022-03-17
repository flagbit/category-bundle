<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Transformer;

/**
 * Interface for denormalization category property data transformers.
 */
interface DenormalizationTransformer
{
    /**
     * Apply transformations to the supplied data on import.
     *
     * @return mixed
     */
    public function transform(string $data);
}
