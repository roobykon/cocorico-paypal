<?php

namespace Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail;

use Doctrine\ORM\Mapping as ORM;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo\Receiver;

/**
 * PaymentInfo
 *
 * @ORM\Table(name="paypal_payment_info")
 * @ORM\Entity
 */
class PaymentInfo
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail", inversedBy="paymentInfo")
     * @ORM\JoinColumn(name="payment_detail_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $paymentDetail;

    /**
     * @var Receiver
     *
     * @ORM\OneToOne(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo\Receiver", mappedBy="receiver")
     */
    private $receiver;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_id", type="string", length=255)
     */
    private $transactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="transaction_status", type="string", length=255)
     */
    private $transactionStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="refunded_amount", type="integer")
     */
    private $refundedAmount;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_pending_refund", type="boolean")
     */
    private $isPendingRefund;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_transaction_id", type="string", length=255)
     */
    private $senderTransactionId;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_transaction_status", type="string", length=255)
     */
    private $senderTransactionStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="pending_reason", type="string", length=255)
     */
    private $pendingReason;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set payment detail
     *
     * @param PaymentDetail $paymentDetail
     * @return PaymentInfo
     */
    public function setPaymentDetail($paymentDetail)
    {
        $this->paymentDetail = $paymentDetail;

        return $this;
    }

    /**
     * Get payment detail
     *
     * @return PaymentDetail
     */
    public function getPaymentDetail()
    {
        return $this->paymentDetail;
    }

    /**
     * Set receiver
     *
     * @param Receiver $receiver
     * @return PaymentInfo
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;

        return $this;
    }

    /**
     * Get receiver
     *
     * @return Receiver
     */
    public function getReceiver()
    {
        return $this->receiver;
    }

    /**
     * Set transactionId
     *
     * @param string $transactionId
     *
     * @return PaymentInfo
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * Get transactionId
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * Set transactionStatus
     *
     * @param string $transactionStatus
     *
     * @return PaymentInfo
     */
    public function setTransactionStatus($transactionStatus)
    {
        $this->transactionStatus = $transactionStatus;

        return $this;
    }

    /**
     * Get transactionStatus
     *
     * @return string
     */
    public function getTransactionStatus()
    {
        return $this->transactionStatus;
    }

    /**
     * Set refundedAmount
     *
     * @param integer $refundedAmount
     *
     * @return PaymentInfo
     */
    public function setRefundedAmount($refundedAmount)
    {
        $this->refundedAmount = $refundedAmount;

        return $this;
    }

    /**
     * Get refundedAmount
     *
     * @return integer
     */
    public function getRefundedAmount()
    {
        return $this->refundedAmount;
    }

    /**
     * Set isPendingRefund
     *
     * @param boolean $isPendingRefund
     *
     * @return PaymentInfo
     */
    public function setIsPendingRefund($isPendingRefund)
    {
        $this->isPendingRefund = (int)(bool)$isPendingRefund;

        return $this;
    }

    /**
     * Get isPendingRefund
     *
     * @return boolean
     */
    public function getIsPendingRefund()
    {
        return (bool)$this->isPendingRefund;
    }

    /**
     * Set senderTransactionId
     *
     * @param string $senderTransactionId
     *
     * @return PaymentInfo
     */
    public function setSenderTransactionId($senderTransactionId)
    {
        $this->senderTransactionId = $senderTransactionId;

        return $this;
    }

    /**
     * Get senderTransactionId
     *
     * @return string
     */
    public function getSenderTransactionId()
    {
        return $this->senderTransactionId;
    }

    /**
     * Set senderTransactionStatus
     *
     * @param string $senderTransactionStatus
     *
     * @return PaymentInfo
     */
    public function setSenderTransactionStatus($senderTransactionStatus)
    {
        $this->senderTransactionStatus = $senderTransactionStatus;

        return $this;
    }

    /**
     * Get senderTransactionStatus
     *
     * @return string
     */
    public function getSenderTransactionStatus()
    {
        return $this->senderTransactionStatus;
    }

    /**
     * Set pendingReason
     *
     * @param string $pendingReason
     *
     * @return PaymentInfo
     */
    public function setPendingReason($pendingReason)
    {
        $this->pendingReason = $pendingReason;

        return $this;
    }

    /**
     * Get pendingReason
     *
     * @return string
     */
    public function getPendingReason()
    {
        return $this->pendingReason;
    }
}

