<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Flagbit\Bundle\CategoryBundle\Entity\CategoryConfig;

class CategoryConfigRepository extends EntityRepository
{
    /**
     * Find the configuration of the category properties.
     *
     * Defaults to 1 as the identifier of the category config row.
     * If there is no such database row, a new instance of {@see CategoryConfig} is returned.
     */
    public function findConfig(int $identifier = 1): CategoryConfig
    {
        /** @phpstan-var CategoryConfig|null $categoryConfig */
        $categoryConfig = $this->find($identifier);

        return $categoryConfig ?? new CategoryConfig([]);
    }
}
