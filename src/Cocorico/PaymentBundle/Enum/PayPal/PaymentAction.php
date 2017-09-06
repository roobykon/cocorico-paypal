<?php

namespace Cocorico\PaymentBundle\Enum\PayPal;

/**
 * Class PaymentAction
 * @package Cocorico\PaymentBundle\Enum\PayPal
 */
class PaymentAction
{
    const SALE = 'Sale';
    const CAPTURE = 'Capture';
    const AUTHORIZATION = 'Authorization';
}
