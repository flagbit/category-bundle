<?php

declare(strict_types=1);

namespace spec\Flagbit\Bundle\CategoryBundle\Transformer\Normalization;

use Flagbit\Bundle\CategoryBundle\Transformer\Normalization\BooleanTransformer;
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

    public function it_returns_one_with_boolean_true_data_argument(): void
    {
        $this->transform(true)->shouldReturn('1');
    }

    public function it_returns_zero_with_boolean_false_data_argument(): void
    {
        $this->transform(false)->shouldReturn('0');
    }
}
