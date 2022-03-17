<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Repository;

use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Flagbit\Bundle\CategoryBundle\Entity\CategoryConfig;
use Flagbit\Bundle\CategoryBundle\Repository\CategoryConfigRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @method findConfig(int $identity = 1)
 */
class CategoryConfigRepositorySpec extends ObjectBehavior
{
    public function let(EntityManagerInterface $em, ClassMetadata $class): void
    {
        $this->beConstructedWith($em, $class);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(CategoryConfigRepository::class);
    }

    public function it_should_return_new_config_when_not_found(EntityManagerInterface $em): void
    {
        $em->find(Argument::any(), Argument::any(), Argument::any(), Argument::any())->willReturn(null);

        $this->findConfig()->shouldEqualConfig([]);
    }

    public function it_should_try_to_find_config_with_specified_id(
        EntityManagerInterface $em,
        CategoryConfig $categoryConfig
    ): void {
        $em->find(Argument::any(), 42, Argument::any(), Argument::any())->willReturn($categoryConfig);

        $this->findConfig(42)->shouldReturn($categoryConfig);
    }

    public function it_should_try_to_find_config_with_default_id(
        EntityManagerInterface $em,
        CategoryConfig $categoryConfig
    ): void {
        $em->find(Argument::any(), 1, Argument::any(), Argument::any())->willReturn($categoryConfig);

        $this->findConfig()->shouldReturn($categoryConfig);
    }

    /**
     * @return Closure[]
     */
    public function getMatchers(): array
    {
        return [
            'equalConfig' => static function (CategoryConfig $subject, array $value) {
                return $subject->getConfig() === $value;
            },
        ];
    }
}
