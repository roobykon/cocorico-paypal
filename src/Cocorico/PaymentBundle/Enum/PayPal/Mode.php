<?php

namespace Cocorico\PaymentBundle\Enum\PayPal;

/**
 * Class Mode
 * @package Cocorico\PaymentBundle\Enum\PayPal
 */
class Mode
{
    const SANDBOX = 'sandbox';
    const LIVE = 'live';

    /**
     * @param string $mode
     * @return bool
     */
    public static function isAvailable($mode)
    {
        return in_array($mode, [
            self::SANDBOX,
            self::LIVE,
        ]);
    }

}