<?php

namespace Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail;

use Doctrine\ORM\Mapping as ORM;
use Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail;

/**
 * Class Sender
 * @package Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail
 *
 * @ORM\Table(name="paypal_sender")
 * @ORM\Entity(repositoryClass="Cocorico\PaymentBundle\Entity\SenderRepository")
 */
class Sender
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
     * @var PaymentDetail
     *
     * @ORM\OneToOne(targetEntity="Cocorico\PaymentBundle\Entity\PayPal\PaymentDetail", inversedBy="paymentDetail", cascade={"remove"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="payment_detail_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $paymentDetail;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_use_credentials", type="boolean")
     */
    private $isUseCredentials;

    /**
     * @var string
     *
     * @ORM\Column(name="tax_id_details", type="string", length=255, nullable=true)
     */
    private $taxIdDetails;

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
     * @return PaymentDetail
     */
    public function getPaymentDetail()
    {
        return $this->paymentDetail;
    }

    /**
     * @param PaymentDetail $paymentDetail
     * @return Sender
     */
    public function setPaymentDetail($paymentDetail)
    {
        $this->paymentDetail = $paymentDetail;

        return $this;
    }

    /**
     * Set isUseCredentials
     *
     * @param boolean $isUseCredentials
     *
     * @return Sender
     */
    public function setIsUseCredentials($isUseCredentials)
    {
        $this->isUseCredentials = (bool)(int)$isUseCredentials;

        return $this;
    }

    /**
     * Get isUseCredentials
     *
     * @return boolean
     */
    public function getIsUseCredentials()
    {
        return (bool)$this->isUseCredentials;
    }

    /**
     * Set taxIdDetails
     *
     * @param string $taxIdDetails
     *
     * @return Sender
     */
    public function setTaxIdDetails($taxIdDetails)
    {
        $this->taxIdDetails = $taxIdDetails;

        return $this;
    }

    /**
     * Get taxIdDetails
     *
     * @return string
     */
    public function getTaxIdDetails()
    {
        return $this->taxIdDetails;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Sender
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
     * @return Sender
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
     * Set accountId
     *
     * @param string $accountId
     *
     * @return Sender
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

