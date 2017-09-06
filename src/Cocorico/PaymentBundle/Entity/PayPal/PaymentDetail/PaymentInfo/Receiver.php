<?php

namespace Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo;

use Doctrine\ORM\Mapping as ORM;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo;

/**
 * Class Receiver
 * @package Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo
 *
 * @ORM\Table(name="paypal_receiver")
 * @ORM\Entity(repositoryClass="Cocorico\PaymentBundle\Repository\ReceiverRepository")
 */
class Receiver
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
     * @var PaymentInfo
     *
     * @ORM\OneToOne(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail\PaymentInfo", inversedBy="paymentInfo", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="payment_info_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $paymentInfo;

    /**
     * @var integer
     *
     * @ORM\Column(name="amount", type="integer")
     */
    private $amount;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_primary", type="boolean")
     */
    private $isPrimary;

    /**
     * @var string
     *
     * @ORM\Column(name="invoice_id", type="string", length=255, nullable=true)
     */
    private $invoiceId;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_type", type="string", length=255)
     */
    private $paymentType;

    /**
     * @var string
     *
     * @ORM\Column(name="payment_sub_type", type="string", length=255, nullable=true)
     */
    private $paymentSubType;

    /**
     * @var string
     *
     * @ORM\Column(name="account_id", type="string", length=255)
     */
    private $accountId;


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
     * @return PaymentInfo
     */
    public function getPaymentInfo()
    {
        return $this->paymentInfo;
    }

    /**
     * @param PaymentInfo $paymentInfo
     */
    public function setPaymentInfo($paymentInfo)
    {
        $this->paymentInfo = $paymentInfo;
    }

    /**
     * Set amount
     *
     * @param integer $amount
     *
     * @return Receiver
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return integer
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Receiver
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Receiver
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set isPrimary
     *
     * @param boolean $isPrimary
     *
     * @return Receiver
     */
    public function setIsPrimary($isPrimary)
    {
        $this->isPrimary = (bool)(int)$isPrimary;

        return $this;
    }

    /**
     * Get isPrimary
     *
     * @return boolean
     */
    public function getIsPrimary()
    {
        return (bool)$this->isPrimary;
    }

    /**
     * Set invoiceId
     *
     * @param integer $invoiceId
     *
     * @return Receiver
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;

        return $this;
    }

    /**
     * Get invoiceId
     *
     * @return integer
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * Set paymentType
     *
     * @param string $paymentType
     *
     * @return Receiver
     */
    public function setPaymentType($paymentType)
    {
        $this->paymentType = $paymentType;

        return $this;
    }

    /**
     * Get paymentType
     *
     * @return string
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * Set paymentSubType
     *
     * @param string $paymentSubType
     *
     * @return Receiver
     */
    public function setPaymentSubType($paymentSubType)
    {
        $this->paymentSubType = $paymentSubType;

        return $this;
    }

    /**
     * Get paymentSubType
     *
     * @return string
     */
    public function getPaymentSubType()
    {
        return $this->paymentSubType;
    }

    /**
     * Set accountId
     *
     * @param string $accountId
     *
     * @return Receiver
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }
}

