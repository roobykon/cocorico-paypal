<?php

namespace Cocorico\PaymentBundle\Service\PayPal;

use Cocorico\PaymentBundle\Enum\PayPal\Ack;
use Cocorico\PaymentBundle\Enum\PayPal\ActionType;
use Cocorico\PaymentBundle\Enum\PayPal\Command;
use Cocorico\PaymentBundle\Enum\PayPal\ErrorLanguage;
use Monolog\Logger;
use PayPal\Service\AdaptivePaymentsService;
use PayPal\Types\AP\PaymentDetailsRequest;
use PayPal\Types\AP\PayRequest;
use PayPal\Types\AP\Receiver;
use PayPal\Types\AP\ReceiverList;
use PayPal\Types\Common\RequestEnvelope;
use PayPal\Types\Common\ResponseEnvelope;

/**
 * Class PayPal
 * @package Cocorico\PaymentBundle\Service\PayPal
 */
class AdaptivePayments
{
    const REDIRECT_URL = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd={cmd}&paykey={paykey}';

    /**
     * @var AdaptivePaymentsService
     */
    protected $service;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var String
     */
    private $error_language = ErrorLanguage::EN_US;

    /**
     * @var String
     */
    private $return_url;

    /**
     * @var String
     */
    private $cancel_url;

    /**
     * AdaptivePayments constructor.
     * @param Logger $logger
     * @param AdaptivePaymentsService $service
     */
    public function __construct(Logger $logger, AdaptivePaymentsService $service)
    {
        $this->service = $service;
        $this->logger = $logger;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $this->return_url = $config['return_url'];
        $this->cancel_url = $config['cancel_url'];

        if (!empty($config['error_language']) && ErrorLanguage::isAvailableLanguage($config['error_language'])) {
            $this->error_language = $config['error_language'];
        }
    }

    /**
     * @param array $receivers
     * @param string $currency_code
     * @param string $return_url
     * @param string $cancel_url
     * @return null|\PayPal\Service\Types\AP\PayResponse
     */
    public function pay(array $receivers, $currency_code, $return_url = '', $cancel_url = '')
    {
        $request = $this->createPayRequest(ActionType::PAY, $currency_code, $receivers, $return_url, $cancel_url);

        try {
            return $this->service->Pay($request);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param string $action_type
     * @param string $currency_code
     * @param array $receivers
     * @param string $return_url
     * @param string $cancel_url
     * @return PayRequest
     */
    private function createPayRequest($action_type, $currency_code, array $receivers, $return_url, $cancel_url)
    {
        $request_envelope = new RequestEnvelope($this->error_language);
        $receivers = $this->createReceiverList($receivers);

        return new PayRequest(
            $request_envelope,
            $action_type,
            $this->getCancelUrl($cancel_url),
            $currency_code,
            $receivers,
            $this->getReturnUrl($return_url)
        );
    }

    /**
     * @param string $return_url
     * @return String
     */
    private function getReturnUrl($return_url)
    {
        return !empty($return_url) ? $return_url: $this->return_url;
    }

    /**
     * @param string $cancel_url
     * @return String
     */
    private function getCancelUrl($cancel_url)
    {
        return !empty($cancel_url) ? $cancel_url: $this->cancel_url;
    }

    /**
     * @param string $pay_key
     * @return PaymentDetailsRequest
     */
    private function createPaymentDetailsRequest($pay_key)
    {
        $request_envelope = new RequestEnvelope($this->error_language);
        $request = new PaymentDetailsRequest($request_envelope);
        $request->payKey = $pay_key;

        return $request;
    }

    /**
     * @param string $pay_key
     * @return null|\PayPal\Service\Types\AP\PaymentDetailsResponse
     */
    public function getPaymentDetailsByPayKey($pay_key)
    {
        $request = $this->createPaymentDetailsRequest($pay_key);
        try {
            return $this->service->PaymentDetails($request);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param string $paykey
     * @return string
     */
    public function getRedirectUrl($paykey)
    {
        return $this->collectRedirectUrl(self::REDIRECT_URL, [
            '{cmd}' => Command::AP_PAYMENT,
            '{paykey}' => $paykey,
        ]);
    }

    /**
     * @param ResponseEnvelope $envelope
     * @return bool
     */
    public function isResponseSuccess(ResponseEnvelope $envelope)
    {
        return Ack::isSuccess($envelope->ack);
    }

    /**
     * @param array $receivers
     * @return ReceiverList
     */
    private function createReceiverList(array $receivers)
    {
        $list = [];
        foreach ($receivers as $receiver) {
            list($email, $amount) = $receiver;
            $list[] = $this->createReceiver($email, $amount);
        }

        return new ReceiverList($list);
    }

    /**
     * @param string $email
     * @param string $amount
     * @return Receiver
     */
    private function createReceiver($email, $amount)
    {
        $receiver = new Receiver($amount);
        $receiver->email = $email;

        return $receiver;
    }

    /**
     * @param string $template
     * @param array $params
     * @return string
     */
    private function collectRedirectUrl($template, array $params)
    {
        return strtr($template, $params);
    }

}
