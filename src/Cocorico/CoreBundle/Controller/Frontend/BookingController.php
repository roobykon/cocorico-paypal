<?php

/*
 * This file is part of the Cocorico package.
 *
 * (c) Cocolabs SAS <contact@cocolabs.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cocorico\CoreBundle\Controller\Frontend;

use Cocorico\CoreBundle\Entity\Booking;
use Cocorico\CoreBundle\Entity\Listing;
use Cocorico\CoreBundle\Event\BookingCreateEvent;
use Cocorico\CoreBundle\Event\BookingEvent;
use Cocorico\CoreBundle\Event\BookingEvents;
use Cocorico\CoreBundle\Event\BookingPayEvent;
use Cocorico\CoreBundle\Model\Manager\BookingManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Booking controller.
 *
 * @Route("/booking")
 */
class BookingController extends Controller
{
    /**
     * Creates a new Booking entity.
     *
     * @Route("/{listing_id}/{start}/{end}/{start_time}/{end_time}/new",
     *      name="cocorico_booking_new",
     *      requirements={
     *          "listing_id" = "\d+"
     *      },
     *      defaults={"start_time" = "00:00", "end_time" = "00:00"}
     * )
     *
     *
     * @Security("is_granted('booking', listing)")
     *
     * @ParamConverter("listing", class="CocoricoCoreBundle:Listing", options={"id" = "listing_id"})
     * @ParamConverter("start", options={"format": "Y-m-d"})
     * @ParamConverter("end", options={"format": "Y-m-d"})
     * @ParamConverter("start_time", options={"format": "H:i"})
     * @ParamConverter("end_time", options={"format": "H:i"})
     *
     * @Method({"GET", "POST"})
     *
     * @param  Request $request
     * @param  Listing $listing
     * @param  \DateTime $start format yyyy-mm-dd
     * @param  \DateTime $end format yyyy-mm-dd
     * @param  \DateTime $start_time format H:i
     * @param  \DateTime $end_time format H:i
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(
        Request $request,
        Listing $listing,
        \DateTime $start,
        \DateTime $end,
        \DateTime $start_time,
        \DateTime $end_time
    )
    {
        $dispatcher = $this->get('event_dispatcher');
        $session = $this->container->get('session');
        $translator = $this->container->get('translator');

        $bookingHandler = $this->get('cocorico.form.handler.booking');
        $booking = $bookingHandler->init(
            $this->getUser(),
            $listing,
            $start,
            $end,
            $start_time,
            $end_time
        );

        //Availability is validated through BookingValidator and amounts are setted through Form Event PRE_SET_DATA
        $form = $this->createCreateForm($booking);

        $success = $bookingHandler->process($form);
        if ($success === 1) {//Success
            try {
                $event = new BookingEvent($booking);
                $dispatcher->dispatch(BookingEvents::BOOKING_NEW_SUBMITTED, $event);
                $response = $event->getResponse();

                if ($response === null) {//No response means we can create new booking
                    $event = new BookingCreateEvent($event->getBooking());
                    $dispatcher->dispatch(BookingEvents::BOOKING_CREATE, $event);

                    if ($event->getIsBookingCreated()) {
                        $booking = $event->getBooking();
                        $return_url = $this->generateAbsoluteUrl('cocorico_booking_return_paypal', ['id' => $booking->getId()]);
                        $cancel_url = $this->generateAbsoluteUrl('cocorico_booking_cancel_paypal', ['id' => $booking->getId()]);

                        $event = new BookingEvent($booking, $return_url, $cancel_url);
                        $dispatcher->dispatch(BookingEvents::BOOKING_NEW_CREATED, $event);

                        $response = new RedirectResponse(
                            $this->generateUrl(
                                'cocorico_dashboard_booking_show_asker',
                                ['id' => $booking->getId()]
                            )
                        );
                    } else {
                        throw new \Exception('booking.new.form.error');
                    }
                }

                return $response;
            } catch (\Exception $e) {
                //Errors message are created in event subscribers
                $session->getFlashBag()->add(
                    'error',
                    /** @Ignore */
                    $translator->trans($e->getMessage(), array(), 'cocorico_booking')
                );
            }
        } elseif ($success === 2) {//Voucher code is valid
            $session->getFlashBag()->add(
                'success_voucher',
                $translator->trans('booking.new.voucher.success', array(), 'cocorico_booking')
            );
        } elseif ($success < 0) {//Errors
            $this->addFlashError($success);
        }

        //Breadcrumbs
        $breadcrumbs = $this->get('cocorico.breadcrumbs_manager');
        $breadcrumbs->addBookingNewItems($request, $booking);

        return $this->render(
            'CocoricoCoreBundle:Frontend/Booking:new.html.twig',
            array(
                'booking' => $booking,
                'form' => $form->createView(),
            )
        );

    }

    /**
     * PayPal return URL
     *
     * @Route("/{id}/return",
     *      name="cocorico_booking_return_paypal",
     *      requirements={
     *          "id" = "\d+"
     *      }
     * )
     *
     * @ParamConverter("booking", class="Cocorico\CoreBundle\Entity\Booking")
     *
     * @param Booking $booking
     * @return RedirectResponse
     */
    public function returnPayPalAction(Booking $booking)
    {
        $event = new BookingPayEvent($booking);
        $this->getEventDispatcher()->dispatch(BookingEvents::BOOKING_PAY, $event);

        return new RedirectResponse($this->generateUrl(
            'cocorico_dashboard_booking_show_asker',
            ['id' => $booking->getId()])
        );
    }

    /**
     * PayPal cancel URL
     *
     * @Route("/{id}/cancel",
     *      name="cocorico_booking_cancel_paypal",
     *      requirements={
     *          "id" = "\d+"
     *      }
     * )
     * @ParamConverter("booking", class="Cocorico\CoreBundle\Entity\Booking")
     *
     * @param Booking $booking
     * @return RedirectResponse
     */
    public function cancelPayPalAction(Booking $booking)
    {
        return new RedirectResponse($this->generateUrl(
            'cocorico_dashboard_booking_show_asker',
            ['id' => $booking->getId()])
        );
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     */
    private function generateAbsoluteUrl($name, array $params)
    {
        return $this->generateUrl($name, $params, UrlGeneratorInterface::ABSOLUTE_URL);
    }


    /**
     * @return EventDispatcher
     */
    private function getEventDispatcher()
    {
        return $this->get('event_dispatcher');
    }

    /**
     * @param $success
     */
    private function addFlashError($success)
    {
        $translator = $this->container->get('translator');
        $errorMsg = $translator->trans('booking.new.unknown.error', array(), 'cocorico_booking');//-4
        $flashType = 'error';

        if ($success == -1) {
            $errorMsg = $translator->trans('booking.new.form.error', array(), 'cocorico_booking');
        } elseif ($success == -2) {
            $errorMsg = $translator->trans('booking.new.self_booking.error', array(), 'cocorico_booking');
        } elseif ($success == -3) {
            $errorMsg = $translator->trans('booking.new.voucher_code.error', array(), 'cocorico_booking');
            $flashType = 'error_voucher';
        } elseif ($success == -4) {
            $errorMsg = $translator->trans('booking.new.voucher_amount.error', array(), 'cocorico_booking');
            $flashType = 'error_voucher';
        }

        $this->container->get('session')->getFlashBag()->add(
            $flashType,
            $errorMsg
        );
    }

    /**
     * Creates a form to create a Booking entity.
     *
     * @param Booking $booking The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Booking $booking)
    {
        $form = $this->get('form.factory')->createNamed(
            '',
            'booking_new',
            $booking,
            array(
                'method' => 'POST',
                'action' => $this->generateUrl(
                    'cocorico_booking_new',
                    array(
                        'listing_id' => $booking->getListing()->getId(),
                        'start' => $booking->getStart()->format('Y-m-d'),
                        'end' => $booking->getEnd()->format('Y-m-d'),
                        'start_time' => $booking->getStartTime() ? $booking->getStartTime()->format('H:i') : "00:00",
                        'end_time' => $booking->getEndTime() ? $booking->getEndTime()->format('H:i') : "00:00"
                    )
                )
            )
        );

        return $form;
    }

}
