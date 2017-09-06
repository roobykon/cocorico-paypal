<?php

namespace Cocorico\PaymentBundle\Enum\PayPal;

/**
 * Class Ack
 * @package Cocorico\PaymentBundle\Enum\PayPal
 */
class Ack
{
    const SUCCESS = 'Success';
    const SUCCESS_WITH_WARNING = 'SuccessWithWarning';
    const FAILURE = 'Failure';
    const FAILURE_WITH_WARNING = 'FailureWithWarning';

    /**
     * @param string $ack
     * @return bool
     */
    public static function isSuccess($ack)
    {
        return in_array($ack, [
            self::SUCCESS,
            self::SUCCESS_WITH_WARNING,
        ]);
    }

    /**
     * @return array
     */
    public static function getDescriptions()
    {
        return [
            self::SUCCESS => 'Операция была проведена успешно',
            self::SUCCESS_WITH_WARNING => 'Операция была проведена успешно, но не идеально. API вернул сообщение, объясняющее, что нужно поправить',
            self::FAILURE => 'Операция не была успешной, в ответе есть коды ошибок, объясняющие отказ',
            self::FAILURE_WITH_WARNING => 'Операция не была успешной, в ответе есть коды ошибок и сообщения, на которые вам стоит взглянуть',
        ];
    }

}