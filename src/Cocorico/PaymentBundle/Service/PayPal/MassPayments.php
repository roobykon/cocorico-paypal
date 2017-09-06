<?php

namespace Cocorico\PaymentBundle\Service\PayPal;

use Cocorico\PaymentBundle\Enum\PayPal\Ack;
use Monolog\Logger;
use PayPal\CoreComponentTypes\BasicAmountType;
use PayPal\EBLBaseComponents\AbstractResponseType;
use PayPal\PayPalAPI\MassPayReq;
use PayPal\PayPalAPI\MassPayRequestItemType;
use PayPal\PayPalAPI\MassPayRequestType;
use PayPal\Service\PayPalAPIInterfaceServiceService;

/**
 * Class MassPayments
 * @package Cocorico\PaymentBundle\Service\PayPal
 */
class MassPayments
{
    /**
     * @var PayPalAPIInterfaceServiceService
     */
    protected $service;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * MassPayments constructor.
     * @param Logger $logger
     * @param PayPalAPIInterfaceServiceService $service
     */
    public function __construct(Logger $logger, PayPalAPIInterfaceServiceService $service)
    {
        $this->service = $service;
        $this->logger = $logger;
    }

    /**
     * @param array $receivers
     * @return null|\PayPal\PayPalAPI\MassPayResponseType
     */
    public function massPay(array $receivers)
    {
        $request = $this->createMassPayRequest($receivers);
        try {
            return $this->service->MassPay($request);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return null;
    }

    /**
     * @param array $receivers
     * @return MassPayReq
     */
    private function createMassPayRequest(array $receivers)
    {
        $request = new MassPayRequestType();
        $request->MassPayItem = [];

        foreach ($receivers as $receiver) {
            $item = new MassPayRequestItemType();
            $item->Amount = new BasicAmountType($receiver['currency_code'], $receiver['amount']);
            $item->ReceiverEmail = $receiver['email'];
            $request->MassPayItem[] = $item;
        }

        $mass_pay_request = new MassPayReq();
        $mass_pay_request->MassPayRequest = $request;

        return $mass_pay_request;
    }

    /**
     * @param AbstractResponseType $response
     * @return bool
     */
    public function isResponseSuccess(AbstractResponseType $response)
    {
        return Ack::isSuccess($response->Ack);
    }

}
