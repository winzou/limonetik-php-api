<?php

/*
 * This file is part of the limonetik-php-api package.
 *
 * (c) Alexandre Bacco
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace winzou\Limonetik;

use Buzz\Client\ClientInterface;
use Buzz\Exception\RequestException;
use Buzz\Message\Request;
use winzou\Limonetik\Buzz\JsonResponse;
use winzou\Limonetik\Exception\InvalidArgumentException;

class APIClient
{
    const VERSION  = 'V40';
    const URL_DEV  = 'https://api.limonetikqualif.com/Rest';
    const URL_PROD = 'https://api.limonetik.com/Rest';

    const EXECCODE_SUCCESS                        = 1000;
    const EXECCODE_INVALID_DATA                   = 2000;
    const EXECCODE_OPERATION_NOT_ALLOWED          = 2500;
    const EXECCODE_OPERATION_REFUSED              = 3000;
    const EXECCODE_DISTANT_SERVER_TECHNICAL_ERROR = 5000;
    const EXECCODE_DISTANT_SERVER_NOT_AVAILABLE   = 5050;

    const EXECCODE_INCORRECT_SYNTAX        = 9400;
    const EXECCODE_AUTH_HEADER_MISSING     = 9401;
    const EXECCODE_FORBIDDEN               = 9403;
    const EXECCODE_NOT_FOUND               = 9404;
    const EXECCODE_HTTP_METHOD_NOT_ALLOWED = 9405;
    const EXECCODE_INTERNAL_ERROR          = 9500;

    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @param array           $options
     * @param ClientInterface $client
     *
     * @throws InvalidArgumentException Ff an option is invalid or missing
     */
    public function __construct(array $options, ClientInterface $client)
    {
        $this->client = $client;

        if (empty($options['key'])) {
            throw new InvalidArgumentException('key');
        }

        if (empty($options['merchantId'])) {
            throw new InvalidArgumentException('merchantId');
        }

        if (!is_bool($options['sandbox'])) {
            throw new InvalidArgumentException('sandbox');
        }

        $this->options = $options;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function PaymentOrderCreate(array $params)
    {
        $request = new Request();
        $request->setMethod('POST');

        foreach (array('PaymentPageId', 'Amount', 'Currency', 'MerchantUrls') as $param) {
            if (!isset($params[$param])) {
                throw new InvalidArgumentException($param);
            }
        }

        $params['MerchantId'] = $this->options['merchantId'];
        $request->setContent(json_encode(array('PaymentOrder' => $params)));

        return $this->doRequest('PaymentOrder/Create', $request)->getContentJson();
    }

    /**
     * @param int   $paymentOrderId
     * @param array $addElements
     *
     * @return array
     */
    public function PaymentOrderDetail($paymentOrderId, array $addElements = array())
    {
        $request = new Request();
        $request->setMethod('GET');

        if (null == $paymentOrderId) {
            throw new InvalidArgumentException('paymentOrderId');
        }

        $qs = array('Id' => $paymentOrderId);

        if ($addElements) {
            $qs['AddElements'] = implode(',', $addElements);
        }

        return $this->doRequest('PaymentOrder/Detail', $request, $qs)->getContentJson();
    }

    /**
     * @param string $merchantOrderId
     * @param array  $addElements
     *
     * @return array
     */
    public function PaymentOrderDetailByMerchant($merchantOrderId, array $addElements = array())
    {
        $request = new Request();
        $request->setMethod('GET');

        if (null == $merchantOrderId) {
            throw new InvalidArgumentException('merchantOrderId');
        }

        $qs = array(
            'MerchantOrderId' => $merchantOrderId,
            'MerchantId'      => $this->options['merchantId']
        );

        if ($addElements) {
            $qs['AddElements'] = implode(',', $addElements);
        }

        return $this->doRequest('PaymentOrder/Detail', $request, $qs)->getContentJson();
    }

    /**
     * @param int    $paymentOrderId
     * @param float  $amount
     * @param string $currency
     *
     * @return array
     */
    public function PaymentOrderCancel($paymentOrderId, $amount, $currency)
    {
        $request = new Request();
        $request->setMethod('POST');

        foreach (array('paymentOrderId', 'amount', 'currency') as $param) {
            if (null == $$param) {
                throw new InvalidArgumentException($param);
            }
        }

        $params = array(
            'PaymentOrderId' => $paymentOrderId,
            'CancelAmount'   => $amount,
            'Currency'       => $currency
        );

        $request->setContent(json_encode($params));

        return $this->doRequest('PaymentOrder/Cancel', $request)->getContentJson();
    }

    /**
     * @param int    $paymentOrderId
     * @param float  $amount
     * @param string $currency
     *
     * @return array
     */
    public function PaymentOrderCharge($paymentOrderId, $amount, $currency)
    {
        $request = new Request();
        $request->setMethod('POST');

        foreach (array('paymentOrderId', 'amount', 'currency') as $param) {
            if (null == $$param) {
                throw new InvalidArgumentException($param);
            }
        }

        $params = array(
            'PaymentOrderId' => $paymentOrderId,
            'ChargeAmount'   => $amount,
            'Currency'       => $currency
        );

        $request->setContent(json_encode($params));

        return $this->doRequest('PaymentOrder/Charge', $request)->getContentJson();
    }

    /**
     * @param int    $paymentOrderId
     * @param float  $amount
     * @param string $currency
     *
     * @return array
     */
    public function PaymentOrderRefund($paymentOrderId, $amount, $currency)
    {
        $request = new Request();
        $request->setMethod('POST');

        foreach (array('paymentOrderId', 'amount', 'currency') as $param) {
            if (null == $$param) {
                throw new InvalidArgumentException($param);
            }
        }

        $params = array(
            'PaymentOrderId' => $paymentOrderId,
            'RefundAmount'   => $amount,
            'Currency'       => $currency
        );

        $request->setContent(json_encode($params));

        return $this->doRequest('PaymentOrder/Refund', $request)->getContentJson();
    }

    /**
     * @parem string  $controller
     * @param Request $request
     * @param array   $qs
     *
     * @throws RequestException
     *
     * @return JsonResponse
     */
    protected function doRequest($controller, Request $request, array $qs = array())
    {
        $request->fromUrl($this->getApiEndpoint($controller).'?'.http_build_query($qs));
        $request->addHeader('Authorization: Basic '.$this->options['key']);
        $request->addHeader('Accept: text/json');

        $this->client->send($request, $response = new JsonResponse());

        $content = $response->getContentJson(true);

        if (!in_array($content['ReturnCode'], array(self::EXECCODE_SUCCESS, self::EXECCODE_OPERATION_REFUSED))) {
            $e = new RequestException(print_r(array_merge($content, array('url' => $request->getUrl(), 'content' => $request->getContent())), true));
            $e->setRequest($request);
            throw $e;
        }

        return $response;
    }

    /**
     * @param string $controller
     *
     * @return string
     */
    protected function getApiEndpoint($controller)
    {
        $url = $this->options['sandbox'] ? self::URL_DEV : self::URL_PROD;

        return $url.'/'.self::VERSION.'/'.$controller;
    }
}
