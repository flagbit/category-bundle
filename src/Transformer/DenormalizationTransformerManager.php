<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Transformer;

use Flagbit\Bundle\CategoryBundle\Repository\CategoryConfigRepository;

/**
 * Handle property denormalization transformer registration and execution.
 *
 * This class handles the registration and execution of denormalization transformers, that allow
 * to apply custom logic for specific property types during denormalization.
 *
 * Symfony service tags are used to register new transformers.
 * The tag used to register a service as a denormalization transformer
 * is "flagbit.category.data_transformer.denormalization".
 * A "type" attribute must be passed with the tag, that defines on which property type the
 * transformer should be applied.
 *
 * Transformers must implement the {@see DenormalizationTransformer} interface.
 */
class DenormalizationTransformerManager
{
    public const DEFAULT_NORMALIZATION_TRANSFORMER = 'default';

    private CategoryConfigRepository $categoryConfigRepository;

    /** @var array<string, DenormalizationTransformer> */
    private array $dataTransformers = [];

    public function __construct(CategoryConfigRepository $categoryConfigRepository)
    {
        $this->categoryConfigRepository = $categoryConfigRepository;
    }

    /**
     * Transform a single property set for a category received during denormalization.
     *
     * @param array<string, mixed> $properties
     *
     * @return array<string, mixed>
     */
    public function transformOnDenormalized(array $properties): array
    {
        $propertyConfig = $this->categoryConfigRepository->findConfig();
        foreach ($propertyConfig->getConfig() as $property => $config) {
            if (! isset($properties[$property])) {
                continue;
            }

            $transformer = $this->dataTransformers[$config['type']]
                ?? $this->dataTransformers[self::DEFAULT_NORMALIZATION_TRANSFORMER];

            foreach ($properties[$property] as $locale => $value) {
                $properties[$property][$locale]['data'] = $transformer->transform($value['data']);
            }
        }

        return $properties;
    }

    /**
     * Add a new denormalization transformer to the registered transformers
     *
     * @param string $type on which the transformer should be applied
     */
    public function addTransformer(DenormalizationTransformer $transformer, string $type): void
    {
        $this->dataTransformers[$type] = $transformer;
    }
}
