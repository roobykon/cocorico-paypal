<?php

namespace Cocorico\CoreBundle\Subscriber;

use Cocorico\CoreBundle\Event\BookingEvent;
use Cocorico\CoreBundle\Event\BookingEvents;
use Cocorico\PaymentBundle\Service\PayPal\AdaptivePayments;
use Lexik\Bundle\CurrencyBundle\Currency\Converter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Class BookingCreatedSubscriber
 * @package Cocorico\CoreBundle\Subscriber
 */
class BookingCreatedSubscriber implements EventSubscriberInterface
{

    /**
     * @var AdaptivePayments
     */
    protected $paypal;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var string
     */
    protected $default_currency;

    /**
     * BookingCreatedSubscriber constructor.
     * @param AdaptivePayments $paypal
     * @param Converter $converter
     * @param Session $session
     * @param string $default_currency
     */
    public function __construct(AdaptivePayments $paypal, Converter $converter, Session $session, $default_currency)
    {
        $this->paypal = $paypal;
        $this->converter = $converter;
        $this->session = $session;
        $this->default_currency = $default_currency;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            BookingEvents::BOOKING_NEW_CREATED => ['onBookingCreated', 1],
        );
    }

    /**
     * @param BookingEvent $event
     * @throws \Exception
     */
    public function onBookingCreated(BookingEvent $event)
    {
        $booking = $event->getBooking();
        $currency = $this->getCurrency();
        $amount = $this->convertAmount($booking->getAmountToPayByAskerDecimal(), $currency);

        $receiver = 'eugene.gichko-facilitator@roobykon.com'; // TODO: get receivers

        $receivers = [
            [$receiver, $amount],
        ];

        $response = $this->paypal->pay($receivers, $currency, $event->getReturnUrl(), $event->getCancelUrl());

        if (!$this->paypal->isResponseSuccess($response->responseEnvelope)) {
            // TODO: booking status for error payment
            throw new \Exception('PP AP pay failed');
        } else {
            $booking->setPayKey($response->payKey);
            $event->setBooking($booking);
        }
    }

    /**
     * @param string $amount
     * @param string $currency
     * @param int $decimals
     * @param bool $round
     * @return string
     */
    private function convertAmount($amount, $currency, $decimals = 2, $round = false)
    {
        return number_format($this->converter->convert($amount, $currency, $round), $decimals);
    }

    /**
     * @return string
     */
    private function getCurrency()
    {
        return $this->session->get('currency', $this->default_currency);
    }

}
