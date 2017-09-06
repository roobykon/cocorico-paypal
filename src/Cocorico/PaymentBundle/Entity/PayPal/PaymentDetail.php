<?php

namespace Cocorico\PaymentBundle\Entity\PayPal;

use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\Sender;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo;

/**
 * PaymentDetail
 *
 * @ORM\Table(name="paypal_payment_detail")
 * @ORM\Entity
 */
class PaymentDetail
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
     * @var PaymentInfo[]
     *
     * @ORM\OneToMany(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo", mappedBy="paymentInfo", cascade={"remove"}, orphanRemoval=true)
     */
    private $paymentInfo;

    /**
     * @var Sender
     *
     * @ORM\OneToOne(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\Sender", mappedBy="sender", cascade={"remove"}, orphanRemoval=true)
     */
    private $sender;

    /**
     * @var string
     *
     * @ORM\Column(name="cancel_url", type="string", length=255)
     */
    private $cancelUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="currency_code", type="string", length=255)
     */
    private $currencyCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ipn_notification_url", type="string", length=255, nullable=true)
     */
    private $ipnNotificationUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="memo", type="string", length=255, nullable=true)
     */
    private $memo;

    /**
     * @var string
     *
     * @ORM\Column(name="return_url", type="string", length=255)
     */
    private $returnUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="sender_email", type="string", length=255)
     */
    private $senderEmail;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="tracking_id", type="string", length=255, nullable=true)
     */
    private $trackingId;

    /**
     * @var string
     *
     * @ORM\Column(name="pay_key", type="string", length=255)
     */
    private $payKey;

    /**
     * @var string
     *
     * @ORM\Column(name="action_type", type="string", length=255)
     */
    private $actionType;

    /**
     * @var string
     *
     * @ORM\Column(name="fees_payer", type="string", length=255)
     */
    private $feesPayer;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_reverse_all_parallel_payments_on_error", type="boolean")
     */
    private $isReverseAllParallelPaymentsOnError;

    /**
     * @var string
     *
     * @ORM\Column(name="preapproval_key", type="string", length=255, nullable=true)
     */
    private $preapprovalKey;

    /**
     * @var string
     *
     * @ORM\Column(name="funding_constraint", type="string", length=255, nullable=true)
     */
    private $fundingConstraint;

    /**
     * @var string
     *
     * @ORM\Column(name="shipping_address", type="string", length=255, nullable=true)
     */
    private $shippingAddress;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pay_key_expiration_date", type="datetimetz", nullable=true)
     */
    private $payKeyExpirationDate;

    /**
     * PaymentDetail constructor.
     */
    public function __construct()
    {
        $this->paymentInfo = new ArrayCollection();
    }

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
     * Add payment info
     *
     * @param PaymentInfo $paymentInfo
     * @return PaymentDetail
     */
    public function addPaymentInfo(PaymentInfo $paymentInfo)
    {
        $paymentInfo->setPaymentDetail($this);
        $this->paymentInfo[] = $paymentInfo;

        return $this;
    }

    /**
     * Remove payment info
     *
     * @param PaymentInfo $paymentInfo
     * @return PaymentDetail
     */
    public function removePaymentInfo(PaymentInfo $paymentInfo)
    {
        $this->paymentInfo->removeElement($paymentInfo);

        return $this;
    }

    /**
     * Get payment info
     *
     * @return PaymentInfo[]
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * Set cancelUrl
     *
     * @param Sender $sender
     *
     * @return PaymentDetail
     */
    public function setSender($sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return Sender
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * Set cancelUrl
     *
     * @param string $cancelUrl
     *
     * @return PaymentDetail
     */
    public function setCancelUrl($cancelUrl)
    {
        $this->cancelUrl = $cancelUrl;

        return $this;
    }

    /**
     * Get cancelUrl
     *
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancelUrl;
    }

    /**
     * Set currencyCode
     *
     * @param string $currencyCode
     *
     * @return PaymentDetail
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;

        return $this;
    }

    /**
     * Get currencyCode
     *
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }

    /**
     * Set ipnNotificationUrl
     *
     * @param string $ipnNotificationUrl
     *
     * @return PaymentDetail
     */
    public function setIpnNotificationUrl($ipnNotificationUrl)
    {
        $this->ipnNotificationUrl = $ipnNotificationUrl;

        return $this;
    }

    /**
     * Get ipnNotificationUrl
     *
     * @return string
     */
    public function getIpnNotificationUrl()
    {
        return $this->ipnNotificationUrl;
    }

    /**
     * Set memo
     *
     * @param string $memo
     *
     * @return PaymentDetail
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Get memo
     *
     * @return string
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Set returnUrl
     *
     * @param string $returnUrl
     *
     * @return PaymentDetail
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }

    /**
     * Get returnUrl
     *
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * Set senderEmail
     *
     * @param string $senderEmail
     *
     * @return PaymentDetail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;

        return $this;
    }

    /**
     * Get senderEmail
     *
     * @return string
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return PaymentDetail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set trackingId
     *
     * @param string $trackingId
     *
     * @return PaymentDetail
     */
    public function setTrackingId($trackingId)
    {
        $this->trackingId = $trackingId;

        return $this;
    }

    /**
     * Get trackingId
     *
     * @return string
     */
    public function getTrackingId()
    {
        return $this->trackingId;
    }

    /**
     * Set payKey
     *
     * @param string $payKey
     *
     * @return PaymentDetail
     */
    public function setPayKey($payKey)
    {
        $this->payKey = $payKey;

        return $this;
    }

    /**
     * Get payKey
     *
     * @return string
     */
    public function getPayKey()
    {
        return $this->payKey;
    }

    /**
     * Set actionType
     *
     * @param string $actionType
     *
     * @return PaymentDetail
     */
    public function setActionType($actionType)
    {
        $this->actionType = $actionType;

        return $this;
    }

    /**
     * Get actionType
     *
     * @return string
     */
    public function getActionType()
    {
        return $this->actionType;
    }

    /**
     * Set feesPayer
     *
     * @param string $feesPayer
     *
     * @return PaymentDetail
     */
    public function setFeesPayer($feesPayer)
    {
        $this->feesPayer = $feesPayer;

        return $this;
    }

    /**
     * Get feesPayer
     *
     * @return string
     */
    public function getFeesPayer()
    {
        return $this->feesPayer;
    }

    /**
     * Set isReverseAllParallelPaymentsOnError
     *
     * @param boolean $isReverseAllParallelPaymentsOnError
     *
     * @return PaymentDetail
     */
    public function setIsReverseAllParallelPaymentsOnError($isReverseAllParallelPaymentsOnError)
    {
        $this->isReverseAllParallelPaymentsOnError = (bool)(int)$isReverseAllParallelPaymentsOnError;

        return $this;
    }

    /**
     * Get isReverseAllParallelPaymentsOnError
     *
     * @return boolean
     */
    public function getIsReverseAllParallelPaymentsOnError()
    {
        return (bool)$this->isReverseAllParallelPaymentsOnError;
    }

    /**
     * Set preapprovalKey
     *
     * @param string $preapprovalKey
     *
     * @return PaymentDetail
     */
    public function setPreapprovalKey($preapprovalKey)
    {
        $this->preapprovalKey = $preapprovalKey;

        return $this;
    }

    /**
     * Get preapprovalKey
     *
     * @return string
     */
    public function getPreapprovalKey()
    {
        return $this->preapprovalKey;
    }

    /**
     * Set fundingConstraint
     *
     * @param string $fundingConstraint
     *
     * @return PaymentDetail
     */
    public function setFundingConstraint($fundingConstraint)
    {
        $this->fundingConstraint = $fundingConstraint;

        return $this;
    }

    /**
     * Get fundingConstraint
     *
     * @return string
     */
    public function getFundingConstraint()
    {
        return $this->fundingConstraint;
    }

    /**
     * Set shippingAddress
     *
     * @param string $shippingAddress
     *
     * @return PaymentDetail
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = $shippingAddress;

        return $this;
    }

    /**
     * Get shippingAddress
     *
     * @return string
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * Set payKeyExpirationDate
     *
     * @param \DateTime $payKeyExpirationDate
     *
     * @return PaymentDetail
     */
    public function setPayKeyExpirationDate($payKeyExpirationDate)
    {
        $this->payKeyExpirationDate = $payKeyExpirationDate;

        return $this;
    }

    /**
     * Get payKeyExpirationDate
     *
     * @return \DateTime
     */
    public function getPayKeyExpirationDate()
    {
        return $this->payKeyExpirationDate;
    }
}

