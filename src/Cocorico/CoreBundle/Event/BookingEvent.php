<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\CoreBundle\Event;

use Cocorico\CoreBundle\Entity\Booking;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Response;

class BookingEvent extends Event
{
    /**
     * @var Booking
     */
    protected $booking;

    /**
     * @var string
     */
    protected $return_url;

    /**
     * @var string
     */
    protected $cancel_url;

    /**
     * @var Response
     */
    protected $response;

    /**
     * BookingEvent constructor.
     * @param Booking $booking
     * @param string $return_url
     * @param string $cancel_url
     */
    public function __construct(Booking $booking, $return_url = '', $cancel_url = '')
    {
        $this->booking = $booking;
        $this->return_url = $return_url;
        $this->cancel_url = $cancel_url;
    }

    /**
     * @return Booking
     */
    public function getBooking()
    {
        return $this->booking;
    }

    /**
     * @param Booking $booking
     */
    public function setBooking(Booking $booking)
    {
        $this->booking = $booking;
    }

    /**
     * @return string
     */
    public function getReturnUrl()
    {
        return $this->return_url;
    }

    /**
     * @param string $return_url
     * @return BookingEvent
     */
    public function setReturnUrl($return_url)
    {
        $this->return_url = $return_url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCancelUrl()
    {
        return $this->cancel_url;
    }

    /**
     * @param string $cancel_url
     * @return BookingEvent
     */
    public function setCancelUrl($cancel_url)
    {
        $this->cancel_url = $cancel_url;

        return $this;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

}
