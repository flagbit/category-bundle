<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer\Denormalization;

use Flagbit\Bundle\CategoryBundle\Transformer\Denormalization\BooleanTransformer;
use PhpSpec\ObjectBehavior;

/**
 * @method transform(mixed $data)
 */
class BooleanTransformerSpec extends ObjectBehavior
{
    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BooleanTransformer::class);
    }

    public function it_returns_true_with_boolean_true_data_argument(): void
    {
        $this->transform(true)->shouldReturn(true);
    }

    public function it_returns_false_with_boolean_false_data_argument(): void
    {
        $this->transform(false)->shouldReturn(false);
    }

    public function it_returns_true_with_int_one_data_argument(): void
    {
        $this->transform(1)->shouldReturn(true);
    }

    public function it_returns_false_with_int_zero_data_argument(): void
    {
        $this->transform(0)->shouldReturn(false);
    }

    public function it_returns_true_with_string_one_data_argument(): void
    {
        $this->transform('1')->shouldReturn(true);
    }

    public function it_returns_false_with_string_zero_data_argument(): void
    {
        $this->transform('0')->shouldReturn(false);
    }
}
