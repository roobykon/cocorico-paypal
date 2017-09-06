<?php

namespace Cocorico\PaymentBundle\Manager\PayPal;

use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail;
use Doctrine\ORM\EntityManager;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\Sender;
use Cocorico\PaymentBundle\Repository\TransactionRepository;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo\Receiver;

/**
 * Class PaymentDetailManager
 * @package Cocorico\PaymentBundle\Manager\PayPal
 */
class PaymentDetailManager
{
    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * PaymentDetailManager constructor.
     * @param EntityManager $entity_manager
     */
    public function __construct(EntityManager $entity_manager)
    {
        $this->entity_manager = $entity_manager;
    }

    /**
     * @param array $details
     * @param array $info
     * @param array $sender
     * @return bool
     */
    public function store(array $details, array $info, array $sender)
    {
        try {
            $details = $this->createPaymentDetail($details);
            $info = $this->createPaymentInfo($details, $info);
            $sender = $this->createSender($sender);
            $sender->setPaymentDetail($details);
            foreach ($info as $payment_info) {
                $details->addPaymentInfo($payment_info);
            }
            $this->entity_manager->flush();
        } catch (\Exception $e) {
//            echo '<pre>';
//            print_r($e->getTraceAsString());
//            echo '</pre>';
//            die;
            return false;
        }

        return true;
    }

    /**
     * @param array $attributes
     * @return PaymentDetail
     */
    private function createPaymentDetail(array $attributes)
    {
        $details = new PaymentDetail();
        $details->setCancelUrl($attributes['cancelUrl']);
        $details->setCurrencyCode($attributes['currencyCode']);
        $details->setIpnNotificationUrl($attributes['ipnNotificationUrl']);
        $details->setMemo($attributes['memo']);
        $details->setReturnUrl($attributes['returnUrl']);
        $details->setSenderEmail($attributes['senderEmail']);
        $details->setStatus($attributes['status']);
        $details->setTrackingId($attributes['trackingId']);
        $details->setPayKey($attributes['payKey']);
        $details->setActionType($attributes['actionType']);
        $details->setFeesPayer($attributes['feesPayer']);
        $details->setIsReverseAllParallelPaymentsOnError($attributes['reverseAllParallelPaymentsOnError']);
        $details->setPreapprovalKey($attributes['preapprovalKey']);
        $details->setFundingConstraint($attributes['fundingConstraint']);
        $details->setShippingAddress($attributes['shippingAddress']);
        $details->setPayKeyExpirationDate($attributes['payKeyExpirationDate']);
        $this->entity_manager->persist($details);

        return $details;
    }

    /**
     * @param PaymentDetail $payment_details
     * @param array $attributes
     * @return PaymentInfo[]
     */
    private function createPaymentInfo(PaymentDetail $payment_details, array $attributes)
    {
        $payment_info = [];
        foreach ($attributes as $attrs) {
            $info = new PaymentInfo();
            $info->setTransactionId($attrs['transactionId']);
            $info->setTransactionStatus($attrs['transactionStatus']);
            $info->setRefundedAmount($attrs['refundedAmount']);
            $info->setIsPendingRefund($attrs['pendingRefund']);
            $info->setSenderTransactionId($attrs['senderTransactionId']);
            $info->setSenderTransactionStatus($attrs['senderTransactionStatus']);
            $info->setPendingReason($attrs['pendingReason']);
            $info->setPaymentDetail($payment_details);
            $receiver = $this->createReceiver($attrs['receiver']);
            $receiver->setPaymentInfo($info);
            $this->entity_manager->persist($info);
        }

        return $payment_info;
    }

    /**
     * @param array $attributes
     * @return Receiver
     */
    private function createReceiver(array $attributes)
    {
        $receiver = new Receiver();
        $receiver->setAmount($attributes['amount']);
        $receiver->setEmail($attributes['email']);
        $receiver->setPhone($attributes['phone']);
        $receiver->setIsPrimary($attributes['primary']);
        $receiver->setInvoiceId($attributes['invoiceId']);
        $receiver->setPaymentType($attributes['paymentType']);
        $receiver->setPaymentSubType($attributes['paymentSubType']);
        $receiver->setAccountId($attributes['accountId']);
        $this->entity_manager->persist($receiver);

        return $receiver;
    }

    /**
     * @param array $attributes
     * @return Sender
     */
    private function createSender(array $attributes)
    {
        $sender = new Sender();
        $sender->setIsUseCredentials($attributes['useCredentials']);
        $sender->setTaxIdDetails($attributes['taxIdDetails']);
        $sender->setEmail($attributes['email']);
        $sender->setPhone($attributes['phone']);
        $sender->setAccountId($attributes['accountId']);
        $this->entity_manager->persist($sender);

        return $sender;
    }

    /**
     * @return TransactionRepository
     */
    public function getRepository()
    {
        return $this->entity_manager->getRepository('CocoricoPaymentBundle:PayPal\PaymentDetail');
    }

    /**
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entity_manager;
    }

}
