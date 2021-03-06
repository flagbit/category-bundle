<?php

declare(strict_types=1);

namespace Flagbit\Bundle\CategoryBundle\Controller\InternalApi;

use Doctrine\ORM\EntityManagerInterface;
use Flagbit\Bundle\CategoryBundle\Entity\CategoryConfig;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryConfigRepository;
use Flagbit\Bundle\CategoryBundle\Schema\SchemaValidator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

use function json_decode;

/**
 * @internal
 */
class CategoryConfigController
{
    private EntityManagerInterface $entityManager;
    private CategoryConfigRepository $repository;
    private NormalizerInterface $normalizer;
    private SchemaValidator $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryConfigRepository $repository,
        NormalizerInterface $normalizer,
        SchemaValidator $validator
    ) {
        $this->entityManager = $entityManager;
        $this->repository    = $repository;
        $this->normalizer    = $normalizer;
        $this->validator     = $validator;
    }

    public function get(int $identifier): Response
    {
        $categoryConfig = $this->findConfig($identifier);

        return new JsonResponse(
            $this->normalizer->normalize($categoryConfig, 'internal_api')
        );
    }

    public function post(Request $request, int $identifier): Response
    {
        if (! $request->isXmlHttpRequest()) {
            return new RedirectResponse('/');
        }

        $config = json_decode($request->request->get('config'), true);

        if ($this->validator->validate($config) !== []) {
            return new JsonResponse([], Response::HTTP_BAD_REQUEST);
        }

        $categoryConfig = $this->findConfig($identifier);
        $categoryConfig->setConfig($config);

        $this->entityManager->persist($categoryConfig);
        $this->entityManager->flush();

        return new JsonResponse([]);
    }

    private function findConfig(int $identifier): CategoryConfig
    {
        /** @phpstan-var CategoryConfig|null $categoryConfig */
        $categoryConfig = $this->repository->find($identifier);
        if ($categoryConfig === null) {
            return new CategoryConfig([]);
        }

        return $categoryConfig;
    }
}
