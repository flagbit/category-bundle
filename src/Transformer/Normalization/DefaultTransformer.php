<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Transformer\Normalization;

use Flagbit\Bundle\CategoryBundle\Transformer\NormalizationTransformer;

/**
 * Default normalization transformer that applies no changes.
 */
class DefaultTransformer implements NormalizationTransformer
{
    /**
     * @phpstan-param mixed $data data that can be cast to string
     */
    public function transform($data): string
    {
        return $data;
    }
}
