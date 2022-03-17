<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer\Normalization;

use Flagbit\Bundle\CategoryBundle\Transformer\Normalization\DefaultTransformer;
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
        $this->transform(5)->shouldReturn(5);
        $this->transform(true)->shouldReturn(true);
        $this->transform(['test' => 5])->shouldReturn(['test' => 5]);
    }
}
