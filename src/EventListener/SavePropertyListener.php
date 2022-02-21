<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\EventListener;

use Akeneo\Pim\Enrichment\Component\Category\Model\CategoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Flagbit\Bundle\CategoryBundle\Entity\CategoryProperty;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryPropertyRepository;
use Flagbit\Bundle\CategoryBundle\Schema\SchemaValidator;
use RuntimeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\ParameterBag;

use function count;

class SavePropertyListener
{
    /** @phpstan-var ParameterBag<mixed> */
    private ParameterBag $propertyValuesBag;
    private CategoryPropertyRepository $repository;
    private EntityManagerInterface $entityManager;
    private SchemaValidator $validator;

    /**
     * @phpstan-param ParameterBag<mixed> $propertyValuesBag
     */
    public function __construct(
        ParameterBag $propertyValuesBag,
        CategoryPropertyRepository $repository,
        EntityManagerInterface $entityManager,
        SchemaValidator $validator
    ) {
        $this->propertyValuesBag = $propertyValuesBag;
        $this->repository        = $repository;
        $this->entityManager     = $entityManager;
        $this->validator         = $validator;
    }

    /**
     * @phpstan-param GenericEvent<mixed> $event
     */
    public function onCategoryPostSave(GenericEvent $event): void
    {
        $category = $event->getSubject();
        if (! $category instanceof CategoryInterface) {
            return;
        }

        $categoryProperty = $this->findProperty($category);

        $properties = $this->propertyValuesBag->all();
        if (count($properties) === 0) {
            return;
        }

        if ($this->validator->validate($properties) !== []) {
            throw new RuntimeException('Invalid properties format');
        }

        $categoryProperty->setProperties($properties);

        $this->entityManager->persist($categoryProperty);
        $this->entityManager->flush();
    }

    private function findProperty(CategoryInterface $category): CategoryProperty
    {
        /** @phpstan-var CategoryProperty|null $categoryProperty */
        $categoryProperty = $this->repository->findOneBy(['category' => $category]);
        if ($categoryProperty === null) {
            $categoryProperty = new CategoryProperty($category);
        }

        return $categoryProperty;
    }
}
