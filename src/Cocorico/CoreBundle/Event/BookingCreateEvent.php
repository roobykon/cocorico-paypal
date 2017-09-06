<?php

namespace Cocorico\CoreBundle\Event;

use Cocorico\CoreBundle\Entity\Booking;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class BookingCreateEvent
 * @package Cocorico\CoreBundle\Event
 */
class BookingCreateEvent extends Event
{
    /**
     * @var Booking
     */
    protected $booking;

    /**
     * @var bool
     */
    private $is_booking_created = false;

    /**
     * BookingCreateEvent constructor.
     * @param Booking $booking
     */
    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * @param Booking $booking
     */
    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * @return Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * @param bool $created
     */
    public function setIsBookingCreated($created = true)
    {
        $this->is_booking_created = $created;
    }

    /**
     * @return bool
     */
    public function getIsBookingCreated()
    {
        return $this->is_booking_created;
    }

}
