<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Transformer\Normalization;

use Flagbit\Bundle\CategoryBundle\Transformer\NormalizationTransformer;

/**
 * Transformer that handles proper normalization of boolean values.
 */
class BooleanTransformer implements NormalizationTransformer
{
    /**
     * Convert passed data to a string representing the boolean value of the data (0 / 1).
     *
     * @phpstan-param bool $data
     *
     * @phpstan-return numeric-string
     */
    public function transform($data): string
    {
        return $data ? '1' : '0';
    }
}
