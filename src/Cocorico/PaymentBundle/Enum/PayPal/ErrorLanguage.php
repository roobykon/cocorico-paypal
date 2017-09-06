<?php

namespace Cocorico\PaymentBundle\Enum\PayPal;

/**
 * Class ErrorLanguage
 * @package Cocorico\PaymentBundle\Enum\PayPal
 */
class ErrorLanguage
{
    const EN_US = 'en_US';

    /**
     * @return array
     */
    public static function getList()
    {
        static $list = [];
        if (empty($list)) {
            $list = [
                self::EN_US => 'United States',
            ];
        }

        return $list;
    }

    /**
     * @param string $lang
     * @return bool
     */
    public static function isAvailableLanguage($lang)
    {
        return array_key_exists($lang, self::getList());
    }

}
