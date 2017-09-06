<?php

namespace Cocorico\PaymentBundle\Controller\PayPal;

use JMS\DiExtraBundle\Annotation as DI;
use Cocorico\PaymentBundle\Enum\PayPal\CurrencyCode;
use Cocorico\PaymentBundle\Service\PayPal\ExpressCheckout;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ExpressCheckoutController
 * @package Cocorico\PaymentBundle\Controller\PayPal
 */
class ExpressCheckoutController extends Controller
{
    /**
     * @var $paypal ExpressCheckout
     *
     * @DI\Inject("cocorico_paypal_ec")
     */
    protected $paypal;

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function setAction(Request $request)
    {
        if ($token = $request->query->get('token')) {
            return new JsonResponse([
                'success' => true,
                'details' => $this->paypal->getExpressCheckoutDetails($token),
            ]);
        }

        $response = $this->paypal->setExpressCheckout(CurrencyCode::USD, '1.00', '0.00');

        if (!$this->paypal->isResponseSuccess($response)) {
            return new JsonResponse([
                'success' => false,
                'response' => $response,
            ], 422);
        }

        return $this->redirect($this->paypal->getRedirectUrl($response->Token));
    }

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function doAction(Request $request)
    {
        $payer_id = $request->query->get('PayerID');
        $token = $request->query->get('token');

        $response = $this->paypal->getExpressCheckoutDetails($token);
        $payment_details = $response->GetExpressCheckoutDetailsResponseDetails->PaymentDetails;

        foreach ($payment_details as $payment) {
            $response = $this->paypal->doExpressCheckout($token, $payer_id, $payment->OrderTotal);
            if (!$this->paypal->isResponseSuccess($response)) {
                return new JsonResponse([
                    'success' => false,
                    'errors' => $response->Errors,
                ], 422);
            }
        }

        return $this->redirect($this->generateUrl('cocorico_paypal_set_express_checkout', [
            'token' => $token,
        ]));
    }

    /**
     * @param string $token
     * @return JsonResponse
     */
    public function getDetailsAction($token)
    {
        $response = $this->paypal->getExpressCheckoutDetails($token);

        if (!$this->paypal->isResponseSuccess($response)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $response->Errors,
            ], 422);
        }

        return new JsonResponse([
            'success' => true,
            'details' => $this->paypal->getExpressCheckoutDetails($token),
        ]);
    }

}
