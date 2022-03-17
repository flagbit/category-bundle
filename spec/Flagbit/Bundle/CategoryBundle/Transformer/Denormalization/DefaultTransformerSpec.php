<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer\Denormalization;

use Flagbit\Bundle\CategoryBundle\Transformer\Denormalization\DefaultTransformer;
use PhpSpec\ObjectBehavior;

/**
 * @method transform(mixed $data)
 */
class DefaultTransformerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(DefaultTransformer::class);
    }

    public function it_returns_unmodified_value(): void
    {
        $this->transform('some test')->shouldReturn('some test');
    }
}
