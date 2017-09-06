<?php

namespace Cocorico\CoreBundle\Subscriber;

use Cocorico\CoreBundle\Event\BookingCreateEvent;
use Cocorico\CoreBundle\Event\BookingEvents;
use Cocorico\CoreBundle\Model\Manager\BookingManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class BookingCreateSubscriber
 * @package Cocorico\CoreBundle\Subscriber
 */
class BookingCreateSubscriber implements EventSubscriberInterface
{
    /**
     * @var BookingManager
     */
    protected $manager;

    /**
     * BookingCreateSubscriber constructor.
     * @param BookingManager $manager
     */
    public function __construct(BookingManager $manager)
    {
        $this->manager = $manager;
    }


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            BookingEvents::BOOKING_CREATE => ['onBookingCreate', 1],
        ];
    }

    /**
     * @param BookingCreateEvent $event
     */
    public function onBookingCreate(BookingCreateEvent $event)
    {
        if ($booking = $this->manager->create($event->getBooking())) {
            $event->setBooking($booking);
            $event->setIsBookingCreated();
        }
    }

}
