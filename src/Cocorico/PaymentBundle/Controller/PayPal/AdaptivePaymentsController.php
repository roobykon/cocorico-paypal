<?php

namespace Cocorico\PaymentBundle\Controller\PayPal;

use JMS\DiExtraBundle\Annotation as DI;
use Cocorico\PaymentBundle\Enum\PayPal\CurrencyCode;
use Cocorico\PaymentBundle\Service\PayPal\AdaptivePayments;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AdaptivePaymentsController
 * @package Cocorico\PaymentBundle\Controller\PayPal
 */
class AdaptivePaymentsController extends Controller
{
    /**
     * @var $paypal AdaptivePayments
     *
     * @DI\Inject("cocorico_paypal_ap")
     */
    private $paypal;

    /**
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function payAction(Request $request)
    {
        $success = $request->query->get('success', null);
        if (!is_null($success)) {
            $pay_key = $this->get('session')->get('paykey', 'Not isset');

            return new JsonResponse([
                'success' => (bool)$success,
                'paykey' => $pay_key,
                'payment_details' => $this->paypal->getPaymentDetailsByPayKey($pay_key),
            ]);
        }

        // test data
        $receivers = [
            ['eugene.gichko-facilitator@roobykon.com', '5.00'],
        ];

        $response = $this->paypal->pay($receivers, CurrencyCode::USD);

        if (!$this->paypal->isResponseSuccess($response->responseEnvelope)) {
            return new JsonResponse([
                'success' => false,
                'error' => $response->error,
            ], 422);
        }

        $this->get('session')->set('paykey', $response->payKey);

        return $this->redirect($this->paypal->getRedirectUrl($response->payKey));
    }

    /**
     * @param string $paykey
     * @return JsonResponse
     */
    public function getPaymentDetailsAction($paykey)
    {
        $response = $this->paypal->getPaymentDetailsByPayKey($paykey);

        if (!$this->paypal->isResponseSuccess($response->responseEnvelope)) {
            return new JsonResponse([
                'success' => false,
                'error' => $response->error,
            ], 422);
        }

        return new JsonResponse([
            'success' => true,
            'response' => $response,
        ]);
    }

}
