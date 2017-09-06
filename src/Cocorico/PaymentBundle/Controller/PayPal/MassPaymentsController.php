<?php

namespace Cocorico\PaymentBundle\Controller\PayPal;

use JMS\DiExtraBundle\Annotation as DI;
use Cocorico\PaymentBundle\Enum\PayPal\CurrencyCode;
use Cocorico\PaymentBundle\Service\PayPal\MassPayments;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class MassPaymentsController
 * @package Cocorico\PaymentBundle\Controller\PayPal
 */
class MassPaymentsController extends Controller
{
    /**
     * @var $paypal MassPayments
     *
     * @DI\Inject("cocorico_paypal_mp")
     */
    protected $paypal;

    /**
     * @return JsonResponse
     */
    public function payAction()
    {
        $response = $this->paypal->massPay([
            ['email' => 'eugene.gichko-buyer@roobykon.com', 'amount' => '1.00', 'currency_code' => CurrencyCode::USD],
            ['email' => 'eugene.gichko.buyer@roobykon.com', 'amount' => '1.00', 'currency_code' => CurrencyCode::USD],
        ]);

        if (!$this->paypal->isResponseSuccess($response)) {
            return new JsonResponse([
                'success' => false,
                'errors' => $response->Errors,
            ], 422);
        }

        return new JsonResponse([
            'success' => true,
            'response' => $response,
        ]);
    }

}
