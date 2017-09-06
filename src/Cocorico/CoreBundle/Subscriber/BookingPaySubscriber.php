<?php

namespace Cocorico\CoreBundle\Subscriber;

use Cocorico\CoreBundle\Entity\Booking;
use Cocorico\CoreBundle\Event\BookingEvents;
use Cocorico\CoreBundle\Event\BookingPayEvent;
use Cocorico\PaymentBundle\Manager\PayPal\PaymentDetailManager;
use Cocorico\PaymentBundle\Service\PayPal\AdaptivePayments;
use Doctrine\ORM\EntityManager;
use PayPal\Types\AP\PaymentDetailsResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BookingPaySubscriber
 * @package Cocorico\CoreBundle\Subscriber
 */
class BookingPaySubscriber implements EventSubscriberInterface
{
    /**
     * @var AdaptivePayments
     */
    protected $paypal;

    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * @var PaymentDetailManager
     */
    protected $paymentDetailManager;

    /**
     * BookingPaySubscriber constructor.
     * @param AdaptivePayments $paypal
     * @param EntityManager $entity_manager
     * @param PaymentDetailManager $paymentDetailManager
     */
    public function __construct(AdaptivePayments $paypal, EntityManager $entity_manager, PaymentDetailManager $paymentDetailManager)
    {
        $this->paypal = $paypal;
        $this->entity_manager = $entity_manager;
        $this->paymentDetailManager = $paymentDetailManager;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BookingEvents::BOOKING_PAY => ['onBookingPay', 1],
        ];
    }

    /**
     * @param BookingPayEvent $event
     */
    public function onBookingPay(BookingPayEvent $event)
    {
        $booking = $event->getBooking();
        $payment_details = $this->paypal->getPaymentDetailsByPayKey($booking->getPayKey());
        $this->storePaymentDetails($payment_details);
        // TODO: check status can set payed
        // TODO: move update booking to booking manager after refactoring him
        $booking->setStatus(Booking::STATUS_PAYED);
        $booking->setPayedBookingAt(new \DateTime());
        $this->entity_manager->persist($booking);
        $this->entity_manager->flush();
        $event->setBooking($booking);
    }

    /**
     * @param PaymentDetailsResponse $details
     * @return bool
     */
    private function storePaymentDetails(PaymentDetailsResponse $details)
    {
        $payment_details = $this->unsetDataKeys((array)$details, [
            'responseEnvelope',
            'paymentInfoList',
            'sender',
            'error',
        ]);

        $payment_info = [];
        foreach ($details->paymentInfoList->paymentInfo as $info) {
            $info = (array)$info;
            if (!empty($info['receiver'])) {
                $info['receiver'] = (array)$info['receiver'];
            }
            $payment_info[] = $info;
        }

        $sender = (array)$details->sender;

        return $this->paymentDetailManager->store($payment_details, $payment_info, $sender);
    }

    /**
     * @param array $data
     * @param array $keys
     * @return array
     */
    private function unsetDataKeys(array $data, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $data)) {
                unset($data[$key]);
            }
        }

        return $data;
    }

}
