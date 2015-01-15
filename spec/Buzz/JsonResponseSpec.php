<?php

/*
 * This file is part of the limonetik-php-api package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\winzou\Limonetik\Buzz;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class JsonResponseSpec extends ObjectBehavior
{
    protected $data = array('foo' => 'bar');

    function it_is_initializable()
    {
        $this->shouldHaveType('winzou\Limonetik\Buzz\JsonResponse');
    }

    function it_should_decode_json()
    {
        $this->setContent(json_encode($this->data));

        $this->getContentJson()->shouldReturn($this->data);
    }

    function it_should_throw_exception_on_invalid_json()
    {
        $this->setContent('azerty');

        $this->shouldThrow('LogicException')->during('getContentJson');
    }

    function it_should_decode_json_with_bom()
    {
        $this->setContent(pack('CCC', 239, 187, 191).json_encode($this->data));

        $this->getContentJson()->shouldReturn($this->data);
    }

    function it_should_throw_exception_on_invalid_json_even_with_bom()
    {
        $this->setContent(pack('CCC', 239, 187, 191).'azerty');

        $this->shouldThrow('LogicException')->during('getContentJson');
    }
}
