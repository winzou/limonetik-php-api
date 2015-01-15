<?php

/*
 * This file is part of the limonetik-php-api package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\winzou\Limonetik;

use Buzz\Client\ClientInterface;
use Buzz\Message\RequestInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use winzou\Limonetik\Buzz\JsonResponse;

class APIClientSpec extends ObjectBehavior
{
    protected $options = array(
        'key'        => 'testKey',
        'merchantId' => 'testId',
        'sandbox'    => true
    );

    protected $createParameters = array(
        'PaymentPageId' => 123,
        'Amount'        => 90,
        'Currency'      => 'EUR',
        'MerchantUrls'  => array()
    );

    function let(ClientInterface $client)
    {
        $this->beConstructedWith($this->options, $client);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('winzou\Limonetik\APIClient');
    }

    function it_should_throw_on_invalid_parameter(RequestInterface $request)
    {
        $this->shouldThrow('InvalidArgumentException')->during('PaymentOrderCreate', array(array()));
    }
}
