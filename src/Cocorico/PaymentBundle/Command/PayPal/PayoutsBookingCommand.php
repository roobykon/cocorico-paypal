<?php

namespace Cocorico\PaymentBundle\Command\PayPal;

use Cocorico\CoreBundle\Repository\BookingRepository;
use Cocorico\PaymentBundle\Enum\PayPal\CurrencyCode;
use Cocorico\PaymentBundle\Service\PayPal\MassPayments;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\Log\LoggerInterface;

/**
 * Class PayoutsBookingCommand
 * @package Cocorico\PaymentBundle\Command\PayPal
 */
class PayoutsBookingCommand extends ContainerAwareCommand
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var MassPayments
     */
    protected $paypal;

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * @var EntityManager
     */
    protected $entity_manager;

    /**
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('payment:paypal:payouts-booking')
            ->setDescription('Payouts booking on PayPal accounts')
            ->setHelp('Mass PayPal payouts');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->setLogger();
        $this->setEntityManager();
        $this->setBookingRepository();
        $this->setPayPal();

        $receivers = [];
        $bookings = $this->repository->findBookingsPaid();
        foreach ($bookings as $booking) {
            $booking->setPayoutBookingAt(new \DateTime);
            $email = $this->getTestPayPalEmail();
            $amount = $booking->getAmountToPayToOffererDecimal();
            $currency = CurrencyCode::USD; // TODO: change currency on EUR
            $receivers[] = [
                'email' => $email, // TODO: add PayPal email to offerer profile
                'amount' => $amount,
                'currency_code' => $currency,
            ];
            $output->writeln(sprintf('<info>[%s]</info> %s %s', $email, $amount, $currency));
        }

        if (!empty($receivers)) {
            $response = $this->paypal->massPay($receivers);
            if (!$this->paypal->isResponseSuccess($response)) {
                $output->writeln(sprintf('<error>[%s]</error>', $response->Ack));
                $this->logger->info(print_r($response->Errors, true));
                $output->writeln($response->Errors[0]->LongMessage);
            } else {
                $this->entity_manager->flush($bookings);
                $output->writeln(sprintf('<info>[%s]</info>', $response->Ack));
                $output->writeln($response->Timestamp);
                $output->writeln($response->CorrelationID);
            }
        } else {
            $output->writeln('<comment>[OK]</comment> No bookings for payouts');
        }
    }

    /**
     * @return string
     */
    private function getTestPayPalEmail()
    {
        static $emails;
        !empty($emails) || $emails = [
            'eugene.gichko.buyer@roobykon.com',
            'eugene.gichko-buyer@roobykon.com',
        ];

        return $emails[array_rand($emails)];
    }

    /**
     * @return EntityManager
     */
    protected function setEntityManager()
    {
        return $this->entity_manager = $this->getContainer()->get('doctrine')->getEntityManager();
    }

    /**
     * @return BookingRepository
     */
    protected function setBookingRepository()
    {
        return $this->repository = $this->entity_manager->getRepository('CocoricoCoreBundle:Booking');
    }

    /**
     * @return MassPayments
     */
    protected function setPayPal()
    {
        return $this->paypal = $this->getContainer()->get('cocorico_paypal_mp');
    }

    /**
     * @return LoggerInterface
     */
    protected function setLogger()
    {
        return $this->logger = $this->getContainer()->get('logger');
    }

}
