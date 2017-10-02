<?php

namespace Cocorico\PaymentBundle\Service\PayPal;

use Cocorico\PaymentBundle\Enum\PayPal\Ack;
use Cocorico\PaymentBundle\Enum\PayPal\Command;
use Cocorico\PaymentBundle\Enum\PayPal\ErrorLanguage;
use Cocorico\PaymentBundle\Enum\PayPal\PaymentAction;
use Monolog\Logger;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AbstractResponseType;
use PayPal\EBLBaseComponents\DoExpressCheckoutPaymentRequestDetailsType;
use PayPal\EBLBaseComponents\PaymentDetailsItemType;
use PayPal\EBLBaseComponents\PaymentDetailsType;
use PayPal\EBLBaseComponents\SetExpressCheckoutRequestDetailsType;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentReq;
use PayPal\PayPalAPI\DoExpressCheckoutPaymentRequestType;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsReq;
use PayPal\PayPalAPI\GetExpressCheckoutDetailsRequestType;
use PayPal\PayPalAPI\SetExpressCheckoutReq;
use PayPal\PayPalAPI\SetExpressCheckoutRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;
use PayPal\Types\AP\Receiver;
use PayPal\Types\AP\ReceiverList;
use Cocorico\PaymentBundle\Enum\PayPal\Mode;

/**
 * Class ExpressCheckout
 * @package Cocorico\PaymentBundle\Service\PayPal
 */
class ExpressCheckout
{
    const SANDBOX_REDIRECT_URL = 'https://www.sandbox.paypal.com/webscr?cmd={cmd}&token={token}';
    const LIVE_REDIRECT_URL = 'https://www.paypal.com/webscr?cmd={cmd}&token={token}';

    /**
     * @var PayPalAPIInterfaceServiceService
     */
    protected $sdk;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var string
     */
    protected $error_language = ErrorLanguage::EN_US;

    /**
     * @var string
     */
    protected $mode = Mode::SANDBOX;

    /**
     * @var string
     */
    private $return_url;

    /**
     * @var string
     */
    private $cancel_url;

    /**
     * @var string
     */
    private $notify_url = '';

    /**
     * PayPal constructor.
     * @param PayPalAPIInterfaceServiceService $service
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(Logger $logger, PayPalAPIInterfaceServiceService $service, array $config)
    {
        $this->sdk = $service;
        $this->logger = $logger;
        $this->setConfig($config);
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config)
    {
        $params = [
            'mode' => function ($mode) {
                return Mode::isAvailable($mode);
            },
            'return_url' => null,
            'cancel_url' => null,
            'error_language' => function ($language) {
                return ErrorLanguage::isAvailableLanguage($language);
            },
        ];


        foreach ($params as $param => $validator) {
            switch (true) {
                case empty($config[$param]);
                case is_callable($validator) && !call_user_func($validator, $config[$param]);
                    continue 2;
                default:
                    $this->$param = $config[$param];
            }
        }
    }

    /**
     * @param string $currency
     * @param string $amount
     * @param string $tax
     * @return \PayPal\PayPalAPI\SetExpressCheckoutResponseType
     */
    public function setExpressCheckout($currency, $amount, $tax)
    {
        $order_total = $this->createAmountType($currency, $amount);
        $tax_total = $this->createAmountType($currency, $tax);

        //TODO: finish him
        $item_details = new PaymentDetailsItemType();
        $item_details->Name = 'sample item';
        $item_details->Amount = $order_total;
        $item_details->Quantity = '1';
        $item_details->ItemCategory = 'Digital';

        $payment_details = new PaymentDetailsType();
        $payment_details->PaymentDetailsItem[0] = $item_details;
        $payment_details->OrderTotal = $order_total;
        $payment_details->PaymentAction = PaymentAction::SALE;
        $payment_details->ItemTotal = $order_total;
        $payment_details->TaxTotal = $tax_total;

        $request_details = new SetExpressCheckoutRequestDetailsType();
        $request_details->PaymentDetails[0] = $payment_details;
        $request_details->CancelURL = $this->cancel_url;
        $request_details->ReturnURL = $this->return_url;
        $request_details->ReqConfirmShipping = 0;
        $request_details->NoShipping = 1;

        $request_type = new SetExpressCheckoutRequestType();
        $request_type->SetExpressCheckoutRequestDetails = $request_details;

        $request = new SetExpressCheckoutReq();
        $request->SetExpressCheckoutRequest = $request_type;

        return $this->sdk->SetExpressCheckout($request);
    }

    /**
     * @param string $currency
     * @param string $value
     * @return BasicAmountType
     */
    private function createAmountType($currency, $value)
    {
        return new BasicAmountType($currency, $value);
    }

    /**
     * @param string $token
     * @param string $payer_id
     * @param BasicAmountType $order
     * @return null|\PayPal\PayPalAPI\DoExpressCheckoutPaymentResponseType
     */
    public function doExpressCheckout($token, $payer_id, BasicAmountType $order)
    {
        $payment_details = $this->createPaymentDetailsType($order);
        $request = $this->createDoExpressCheckoutPaymentRequest($token, $payer_id, $payment_details);
        try {
            return $this->sdk->DoExpressCheckoutPayment($request);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param string $token
     * @return null|\PayPal\PayPalAPI\GetExpressCheckoutDetailsResponseType
     */
    public function getExpressCheckoutDetails($token)
    {
        $getExpressCheckoutDetailsRequest = new GetExpressCheckoutDetailsRequestType($token);
        $getExpressCheckoutReq = new GetExpressCheckoutDetailsReq();
        $getExpressCheckoutReq->GetExpressCheckoutDetailsRequest = $getExpressCheckoutDetailsRequest;
        try {
            return $this->sdk->GetExpressCheckoutDetails($getExpressCheckoutReq);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param array $receivers
     * @return ReceiverList
     */
    public function createReceiverList(array $receivers)
    {
        $list = [];
        foreach ($receivers as $receiver) {
            list($email, $amount) = $receiver;
            $list[] = $this->createReceiver($email, $amount);
        }

        return new ReceiverList($list);
    }

    /**
     * @param AbstractResponseType $response
     * @return bool
     */
    public function isResponseSuccess(AbstractResponseType $response)
    {
        return Ack::isSuccess($response->Ack);
    }

    /**
     * @param string $token
     * @return string
     */
    public function getRedirectUrl($token)
    {
        $url = $this->mode == Mode::LIVE ? self::LIVE_REDIRECT_URL : self::SANDBOX_REDIRECT_URL;

        return $this->collectRedirectUrl($url, [
            '{cmd}' => Command::EXPRESS_CHECKOUT,
            '{token}' => $token,
        ]);
    }

    /**
     * @param BasicAmountType $order_total
     * @return PaymentDetailsType
     */
    private function createPaymentDetailsType(BasicAmountType $order_total)
    {
        $payment_details_type = new PaymentDetailsType();
        $payment_details_type->OrderTotal = $order_total;
        if (!empty($notify_url)) {
            $payment_details_type->NotifyURL = $this->notify_url;
        }

        return $payment_details_type;
    }

    /**
     * @param string $token
     * @param string $payer_id
     * @param PaymentDetailsType $payment_details
     * @return DoExpressCheckoutPaymentReq
     */
    private function createDoExpressCheckoutPaymentRequest($token, $payer_id, PaymentDetailsType $payment_details)
    {
        $request_details_type = new DoExpressCheckoutPaymentRequestDetailsType();
        $request_details_type->PayerID = $payer_id;
        $request_details_type->Token = $token;
        $request_details_type->PaymentAction = PaymentAction::SALE;
        $request_details_type->PaymentDetails[0] = $payment_details;

        $request_type = new DoExpressCheckoutPaymentRequestType();
        $request_type->DoExpressCheckoutPaymentRequestDetails = $request_details_type;

        $request = new DoExpressCheckoutPaymentReq();
        $request->DoExpressCheckoutPaymentRequest = $request_type;

        return $request;
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
